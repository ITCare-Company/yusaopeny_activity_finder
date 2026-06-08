# AF4 Vue 2 → Vue 3 Migration Reference

Exact breaking-change surface in `openy_af4_vue_app/`, with file + line
anchors — the source of truth for the rewrite phases (analog of the canvas
queue's `STYLE-REFERENCE.md`). Measured on the fork `6.x` branch. Update
after each phase ships.

> Canonical breaking-change list: [Vue 3 Migration Guide](https://v3-migration.vuejs.org/).
> This doc records only what **actually appears in AF4**.

---

## Dependency deltas

| Package | Current (Vue 2) | Target (Vue 3) | Notes |
|---|---|---|---|
| `vue` | `^2.6.14` | `^3.4` | core |
| `vue-router` | `^3.5.3` | `^4` | `createRouter` / `createWebHistory` |
| `vue-template-compiler` | `^2.6.14` | **removed** | replaced by `@vue/compiler-sfc` (pulled by build tool) |
| `bootstrap-vue` | `^2.22.0` | **no Vue 3 build** | W1 decision: replacement |
| `bootstrap` | `^4.6.1` | `^4` keep / `^5` if replacement needs it | tied to W1-P1 |
| `@fortawesome/vue-fontawesome` | `^2.0.6` | `^3` | Vue 3 build |
| `@iconify/vue2` | `^2.1.0` | `@iconify/vue` `^4` | package rename |
| `@vue/cli-service` | `^4.5.13` | `^5` **or** Vite | W1-P0 decision |
| `eslint-plugin-vue` | `^5` | `^9` | Vue 3 rules |
| `@vue/eslint-config-prettier` / `prettier` | `^5.1.0` / `^1.18.2` | bump | lint pass W5-P5 |
| `axios` | `^x` (external) | **removed** | replace with native `fetch` — W5-P6 |

---

## 1. Global app bootstrap — `src/main.js`

**Vue 2 (current):**

```js
import Vue from 'vue'
Vue.config.devtools = process.env.NODE_ENV === 'development'
import BootstrapVue from 'bootstrap-vue'
Vue.component('font-awesome-icon', FontAwesomeIcon)
Vue.config.productionTip = false
Vue.use(BootstrapVue)
Vue.filter('capitalize', …)
Vue.filter('t', …)
Vue.filter('formatPlural', …)
Vue.mixin({ computed: { isIosMobile }, methods: { trackEvent, t, formatPlural, getCookie } })
new Vue({ router, components: { 'activity-finder': App } }).$mount('#activity-finder')
```

**Vue 3 (target shape):**

```js
import { createApp } from 'vue'
const app = createApp(App)
app.config.globalProperties.$xyz = …   // for the mixin methods, or a composable
app.component('font-awesome-icon', FontAwesomeIcon)
app.use(router)
// app.use(<bootstrap replacement>)     // per W1-P1
// filters → methods/computed/global props (see §3)
app.mount('#activity-finder')
```

**Watch-outs:**

- `Vue.config.productionTip` / `Vue.config.devtools` are **removed** in Vue 3
  (devtools auto-detect; no productionTip). Drop them.
- The current bootstrap registers a wrapper component `'activity-finder': App`
  and mounts `#activity-finder`. Vue 3 `createApp(App).mount('#activity-finder')`
  mounts `App` directly. **Confirm the Drupal template mounts on
  `#activity-finder` and the `App` root renders `#activity-finder-app`** —
  preserve both ids (W0-P0 records them).
- The GA `document.addEventListener('openy_activity_finder_event', …)` block is
  framework-agnostic — keep as-is.

## 2. Router — `src/router/index.js`

**Vue 2:** `Vue.use(VueRouter); new VueRouter({ mode: 'history', routes: [] })`

**Vue 3:** `createRouter({ history: createWebHistory(), routes: [] })`

`routes` is **empty** — the router is vestigial (AF4 drives screens via `step`
state in `App.vue`, not routes). W3-P1 confirms whether the router can be
dropped entirely or must stay for `history` side effects. **Do not assume —
verify against `App.vue` usage of `$router`/`$route` first.**

## 3. Template filters — REMOVED in Vue 3

`Vue.filter` and the `{{ value | filter }}` syntax are **gone**. AF4 has:

| Filter | Defined | Template usages (approx) | Vue 3 replacement |
|---|---|---|---|
| `t` | `main.js` + `Vue.mixin` method `t` | ~88 | call `t(...)` method (already exists on the mixin) |
| `formatPlural` | `main.js` + mixin method | ~12 | call `formatPlural(...)` method (already exists) |
| `capitalize` | `main.js` only | ~3 | small method/computed or inline |

**Key advantage:** `t` and `formatPlural` already exist as **mixin methods**,
so `{{ x | t }}` → `{{ t(x) }}` reuses the same implementation. `capitalize`
exists only as a filter — add a method. **i18n parity is mandatory**: keep the
`context: 'Activity Finder'` argument and `window.Drupal.t/formatPlural`
calls byte-identical (RULES → "i18n parity"). W5-P0 owns this conversion.

## 4. BootstrapVue — no Vue 3 build

Consumers (7 source files + `main.js`):

| File | What it uses (verify on read) |
|---|---|
| `components/modals/Modal.vue` | `b-modal` / `$bvModal` — core modal shell, depended on by every modal |
| `components/Fieldset.vue` | b-* form control |
| `components/Foldable.vue` | b-* collapse/toggle |
| `components/FoldableInput.vue` | b-* collapse/input |
| `components/filters/Ages.vue` | b-* form control |
| `components/steps/SelectAges.vue` | b-* form control |
| `components/ResultsBar.vue` | b-* (likely `b-dropdown`/`b-modal` trigger) |

Replacement options (decided in W1-P1, **not** prejudged here): BootstrapVue
Next (`bootstrap-vue-next`, Vue 3, still maturing) · plain Bootstrap 5 markup +
small custom components · hand-rolled replacements for the handful of widgets
actually used. **Decision criterion:** smallest surface that preserves the
existing markup/classes so the W0 visual baseline still matches.

## 5. Slots

`v-slot` (used in 17 components incl. `App.vue`, `ResultsBar.vue`, the steps)
is **compatible** in Vue 3. Only legacy `slot="x"` / `slot-scope="x"`
attribute syntax needs rewriting — W5-P3 greps for those and fixes any found.

## 6. Not present in AF4 (smaller scope than AF3/CF)

Confirmed absent on audit — **do not write phases for these**:

- No global `EventBus` / `new Vue()` event bus, no `$on` / `$off` /
  `$root.$emit`. (AF3 and Camp Finder use these; AF4 does not.)
- No `.sync` modifiers.
- No `$listeners` / `$children` / `$attrs` fall-through usage.

## 7. Critical Migration Gaps & Best Practices

Based on research and environmental analysis, three critical architectural gaps must be addressed:

### A. Vue runtime delivery — Vue 2 vs Vue 3 on the same Drupal site (W1-D4 decision)
- **Problem:** Other modules on the site (AF3 and Camp Finder) still run Vue 2 and depend on the global `window.Vue` (Vue 2) provided by `openy_system/vue`. `@vue/cli-service` library mode **auto-externalizes `vue`** (it is not even in `vue.config.js` externals, which only lists `vue-router`/`axios`/`bootstrap-vue`). A naive Vue 3 build would resolve `vue` to the Vue 2 global and crash.
- **Two paths — choose in W1-D4, do not pre-drop anything from `libraries.yml`:**
  - **Bundle (recommended default):** bundle Vue 3 + Vue Router 4 **inside** `activity_finder_4.umd.min.js` (self-contained micro-frontend). Build-config work only — **override the auto-external so `vue` is bundled, not external** (W2-P0 / W3-P0). `libraries.yml` is **left as-is**; the `openy_system/vue` + `openy_system/vue-router` deps become inert but harmless. **No mandatory drop.**
  - **Externalize:** only **when there is actually something to externalize** — register `openy_system/vue3` (+ vue-router 4) exposing `window.Vue3`, map the build externals to it, then update the `activity_finder_4` library deps. Consumer-contract change → `DECISIONS.md` entry; done at ship, not speculatively.
- **Criterion:** bundling avoids touching `libraries.yml` and any global-collision risk; externalizing shares one Vue 3 copy across future Vue 3 modules. Default to bundle unless a second Vue 3 consumer already exists.

### B. Bootstrap 4 Class/Styling Parity
- **Problem:** Modern Vue 3 Bootstrap libraries like `bootstrap-vue-next` target Bootstrap 5. However, the parent Drupal theme and pages operate on Bootstrap 4 (`bootstrap ^4.6.1` is in `package.json`). Loading Bootstrap 5 CSS will break the main site styles.
- **Solution:** The BootstrapVue replacement strategy must use custom wrapper components or template markup that compiles against Bootstrap 4 utility classes (e.g., using `mr-2` instead of `me-2`, preserving BS4 grids/flexbox structure).

### C. Migration Build (`@vue/compat`) for De-risking
- **Problem:** Swapping Vue 2 for Vue 3 directly might lead to hidden runtime exceptions due to deprecated APIs (e.g., event emitter behavior, slot scope syntax).
- **Solution:** Configure `@vue/compat` (the Vue 3 Migration Build) in development mode for waves W3/W4. This logs deprecation warnings in the browser console, allowing incremental refactoring of individual component deprecations before switching to pure Vue 3 for the production build.

## 8. Drupal consumer contract (must survive)

| Contract point | Value | Recorded in |
|---|---|---|
| Library id | `activity_finder_4` | `openy_activity_finder.libraries.yml:62` |
| JS artifact | `openy_af4_vue_app/dist/activity_finder_4.umd.min.js` | `libraries.yml:65` |
| CSS artifact | `openy_af4_vue_app/dist/activity_finder_4.css` | `libraries.yml:68` |
| UMD global / build name | `activity_finder_4` | `package.json` build script `--name activity_finder_4` |
| Mount element | `#activity-finder` | `main.js` `$mount` |
| Root render id | `#activity-finder-app` | `App.vue` template |
| Runtime deps | `window.Drupal.t`, `window.Drupal.formatPlural`, `window.drupalSettings.path.baseUrl` | `main.js`, `client/index.js` |

W0-P0 verifies and freezes this table; W7-P0 re-checks the produced build
against it.

## 9. HTTP client — `axios` → native `fetch` (W5-P6)

- **Usage:** one file — `src/client/index.js` (`import axios from 'axios'` +
  one `axios.create({...})` instance). No other AF4 source imports axios.
- **Delivery:** `vue.config.js` externals `axios`; backed by the Drupal library
  `openy_system/axios` on the `activity_finder_4` library deps.
- **Target:** a small `fetch` wrapper with the same base URL
  (`window.drupalSettings.path.baseUrl`), JSON handling, and error semantics the
  callers rely on. Endpoints unchanged (`af/get-data`,
  `af/api/v1/session-data`, `af/more-info`).
- **Cleanup:** drop `axios` from `package.json` + `vue.config.js` externals;
  remove the `openy_system/axios` library dep once confirmed unused.

## 10. PHP backend — pluggable (W0b, separate concern)

Not a Vue change — runs on the current Vue 2 app, lands before the migration.

- **Today:** `OpenyActivityFinderBackendInterface` is implemented by
  `OpenyActivityFinderSolrBackend` (and a Daxko impl in `openy_daxko2` when that
  module is enabled). The backend is resolved by a **global config service-id**
  — `ActivityFinder4Block::getBackend()` does
  `\Drupal::service($settings->get('backend'))` (`openy_activity_finder.settings.backend`,
  Solr by default). No per-block choice, no plugin discovery. Consumers
  (`ActivityFinderController`, `ActivityFinder4Block`) resolve that service-id.
- **Target:** a **Drupal plugin type** `ActivityFinderBackend` (manager +
  attribute discovery + base) with plugins `solr` (extracted, default), `mock`
  (fixtures, no Solr), `db` (entity query, no Solr). Backend chosen in the
  **block config form**; default `solr` keeps existing sites unchanged. A
  block with no per-block value falls back to the **global `settings.backend`**
  so non-Solr sites are not silently flipped (W0b-P5). Daxko is handled only
  when its module is enabled — currently skipped.
- **Why it gates the migration:** the **Mock** backend lets AF4 run without a
  Solr stack, so the Vue 2 → Vue 3 work (and W0-P1 baseline) can proceed on any
  box. Detail in [`W0b-backend-harness/`](W0b-backend-harness/).

### Backend response schema (the output contract every plugin must emit)

Once the backend is a plugin type — and especially once a block may run **more
than one** backend — every plugin must return the **same normalized response
shape** so the Vue app (and any aggregation across backends) consumes them
uniformly. Today this shape is **implicit** (whatever `OpenyActivityFinderSolrBackend`
happens to return); W0b makes it **explicit and documented**. Measured on the
Solr backend (`src/OpenyActivityFinderSolrBackend.php`) — not invented:

`runProgramSearch($parameters, $log_id)` returns:

| Key | Type | Notes (canonical: Solr backend) |
|---|---|---|
| `count` | int | total result count (`:129`) |
| `facets` | map | `field → [ { id, label, count, filter } ]` (`:132`, `:572`) |
| `pager` | int | current page index (`:135`) |
| `pager_info` | map | page structure from `getPages()` (`:138`) |
| `table` | list | result rows (`:141`) — each row: `nid`, `name`, `dates`, `weeks`, `schedule`, `days`, `times`, `location`, `location_id`, `location_info`, `instructor`, `availability_note`, `availability_status`, `activity_type`, `link`, `log_id` (`:500‑518`) |
| `groupedLocations` | list | locations + per-location `count` (`:163`) |
| `sort` | string | e.g. `title__ASC` (`:160`) |
| `error` | string | **only** on backend failure (`:125`) — present ⇒ results absent |
| **`externals`** | map | **NEW, this wave.** Open key-value bag for **backend-specific** data that does not fit the common schema (e.g. Daxko-only fields). Common consumers ignore it; bespoke consumers read it. Keeps the shared schema stable across plugins while letting any backend attach extras. |

- The nested shapes (`table` row, `facets` entry, `groupedLocations`) are
  **defined by the Solr backend as the reference**; Mock and DB must match them
  byte-for-byte at the consumer boundary (D5). Their own extras go in
  `externals`, never as new top-level keys.
- The other interface methods (`getLocations`, `getSortOptions`, `getAges`,
  `getCategories`, `getProgramsMoreInfo`, `getDaysOfWeek`, `getPartsOfDay`)
  carry their existing return shapes; W0b-P1 records each from the Solr backend
  so Mock/DB reproduce them.
- **Multi-backend (one block, ≥1 backend):** a uniform response schema is the
  precondition for aggregating N backends; per-backend differences live in
  `externals`, not in divergent top-level keys. The **aggregation rule** (how N
  responses combine) is a separate locked-pending decision — see W0b
  `DECISIONS.md` D11.
