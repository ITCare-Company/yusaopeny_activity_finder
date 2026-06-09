# W0-P0 Drupal Consumer Contract — Findings

Verified against actual files on 2026-06-09.
Site: af4-migration.ddev.site (7.x branch, Mock backend).

---

## Contract table

| Point | Value | Source |
|---|---|---|
| Library ID | `activity_finder_4` | `openy_activity_finder.libraries.yml` |
| JS bundle | `openy_af4_vue_app/dist/activity_finder_4.umd.min.js` | libraries.yml |
| CSS bundle | `openy_af4_vue_app/dist/activity_finder_4.css` | libraries.yml |
| UMD global name | `activity_finder_4` | libraries.yml (filename convention) |
| Mount element | `<div id="activity-finder">` | `templates/openy-activity-finder-4-block.html.twig` |
| Vue root component | `<activity-finder>` | same twig template |
| Vue bootstrap | `new Vue({render: h => h(App)}).$mount('#activity-finder')` | `openy_af4_vue_app/src/main.js` |

---

## External runtime dependencies (via libraries.yml)

These are loaded BEFORE the AF4 bundle. Migration must preserve or replace each:

| Library | Version | How loaded |
|---|---|---|
| Vue 2 | via `openy_system/vue` | externalized (not bundled) |
| Vue Router | via `openy_system/vue-router` | externalized |
| axios | via `openy_system/axios` | externalized |
| BootstrapVue | 2.1.0 CDN | `openy_activity_finder/bootstrap-vue` (external CDN) |
| Popper.js | 1.16.0 CDN | `openy_activity_finder/popper` (external CDN) |

**W1/W3 implication:** Vue is externalized via `openy_system/vue`. After Vue 3 migration,
decision needed (W1-D4): bundle Vue 3 into UMD OR add `openy_system/vue3` to openy_system.

---

## Props passed from Drupal to Vue (twig → Vue component)

All passed as JSON-encoded `:prop` bindings on `<activity-finder>`:

- `:backend` — list of backend plugin IDs (e.g. `["mock"]`)
- `:backend-service` — legacy service class name (fallback)
- `:ages`, `:days`, `:times`, `:days-times`, `:weeks`, `:durations`, `:start-months` — filter data
- `:categories`, `:categories-type`, `:activities` — program taxonomy
- `:locations` — location list
- `:sort-options`, `:default-sort-option`, `:relevance-sort-option` — sort config
- `:limit-by-location`, `:limit-by-category`, `:exclude-by-location`, `:exclude-by-category` — content scope
- `:legacy-mode`, `:skip-wizard` — UI mode flags
- `:bs-version` — Bootstrap version hint (for BootstrapVue compatibility)
- `:background-image`, `:disable-spots-available`, `:disable-search-box` — display options
- `:filters-section-config` — accordion/expander config

**Migration note:** All these props are defined in `src/Plugin/Block/ActivityFinder4Block.php → build()`.
The Vue 3 component must accept the same prop names — no rename without Drupal-side change.

---

## API endpoints AF4 calls at runtime

From `openy_af4_vue_app/src/client/index.js` (axios-based, W5-P6 replaces with fetch):

| Endpoint | Purpose |
|---|---|
| `GET /af/get-data` | Main data: facets, results, counts. Params: `backend[]`, filters |
| `GET /af/api/v1/session-data` | Session detail for modal |
| `GET /af/more-info` | Additional session info |

Backend contract: these routes hit `src/Controller/ActivityFinderController.php`
which delegates to the plugin factory. Unchanged by Vue migration.

---

## W7 acceptance checklist (generated from this table)

After Vue 3 build:
- [ ] Library ID `activity_finder_4` unchanged in libraries.yml
- [ ] Bundle path `openy_af4_vue_app/dist/activity_finder_4.umd.min.js` unchanged
- [ ] `<div id="activity-finder">` mount point unchanged in twig
- [ ] `<activity-finder>` root component tag unchanged
- [ ] All prop names from table above accepted by Vue 3 component
- [ ] `/af/get-data`, `/af/api/v1/session-data`, `/af/more-info` still called correctly
- [ ] Vue runtime dependency updated (openy_system/vue → vue3 OR bundled)
- [ ] BootstrapVue dependency removed from libraries.yml (replaced in W4)

---

## Flags for W7

- **Vue external dep**: `openy_system/vue` must be replaced or removed. Decision in W1-D4.
- **BootstrapVue CDN**: `openy_activity_finder/bootstrap-vue` and `popper` entries in
  libraries.yml must be removed after W4 (no Vue 3 BootstrapVue).
- **`:bs-version` prop**: passed to Vue. After BootstrapVue removal, this prop becomes
  unused. Can be kept (ignored) or removed with Drupal-side cleanup in W7.
