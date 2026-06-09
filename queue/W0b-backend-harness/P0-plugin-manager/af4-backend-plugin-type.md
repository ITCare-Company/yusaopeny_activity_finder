# af4-backend-plugin-type — Backend plugin type + block selector

## Goal

Turn the global-config-resolved backend (`settings.backend` service-id) into a
**Drupal plugin type` ActivityFinderBackend`, and let each AF4 block
**choose** its backend in the block config form. Solr stays the default — no
existing site changes.

## Files

- `src/OpenyActivityFinderBackendInterface.php` — **read-only** reference; the
  plugin interface (it already defines the backend contract). Plugins implement
  it.
- New: `src/Attribute/ActivityFinderBackend.php` — the plugin attribute
  (`id`, `label`; annotation fallback if the core target predates attribute
  discovery).
- New: `src/ActivityFinderBackendManager.php` — `DefaultPluginManager`
  discovering `Plugin/ActivityFinderBackend/`.
- New: `src/Plugin/ActivityFinderBackend/ActivityFinderBackendPluginBase.php` —
  base implementing `OpenyActivityFinderBackendInterface` (shared helpers from
  the current `OpenyActivityFinderBackend` base move here).
- `openy_activity_finder.services.yml` — register the manager service; keep the
  legacy `openy_activity_finder.solr_backend` service alias during transition
  (removed once consumers resolve via the manager).
- `src/Plugin/Block/ActivityFinder4Block.php` —
  `defaultConfiguration()` adds `backend_plugin: 'solr'`; `blockForm()` adds a
  `select` populated from the manager's definitions; `blockSubmit()` stores it;
  the render path resolves the chosen plugin via the manager and **passes the chosen plugin id to the frontend** (via `drupalSettings` or a `data-backend` mount attribute).
- `src/Controller/ActivityFinderController.php` — resolve the backend via the
  manager (block-config-aware via query parameter `?backend=`) instead of the injected fixed service.

## Steps

1. Define the attribute + manager + base, discovering
   `src/Plugin/ActivityFinderBackend/`. Ensure the plugin manager handles Attribute discovery natively, documenting any Annotation fallback needed for older Drupal core compatibility (pre-10.2).
2. Wire the manager into `services.yml`. Keep the old service as an alias so
   nothing breaks before P1 extracts Solr.
3. Add `backend_plugin` to the block `defaultConfiguration()` (default
   `'solr'`), a labelled `select` in `blockForm()`, persistence in
   `blockSubmit()`.
4. Attach the selected plugin ID to the frontend (e.g. `drupalSettings.openy_activity_finder.backend_plugin` or element `data-backend`).
5. Resolve the chosen plugin id → backend instance via the manager at render
   (block + controller). In the controller, read the backend from request query parameters (fall back to `'solr'` or the global `settings.backend` config if empty). W0b-P5 replaces this literal default with a read of the global `settings.backend` config so non-Solr sites are not silently flipped.
6. `drush cr`; confirm the plugin type is discovered (`drush
   php:eval` listing manager definitions) and the block form shows the select.

## Tests

```sh
ddev drush cr
ddev drush php:eval '$m=\Drupal::service("plugin.manager.activity_finder_backend"); print_r(array_keys($m->getDefinitions()));'
# Expect the discovered backend ids once P1–P3 land; after P0 alone, the type
# exists and resolves the legacy Solr backend as default.
```

Load a page with the AF4 block, open its config form, confirm the **Backend**
select is present and defaults to Solr. AF4 still renders via Solr unchanged.

## Validation

Owner approves. Plugin type discovered; block form has the selector defaulting
to `solr`; existing blocks (no stored `backend_plugin`) resolve to Solr
(provisional — W0b-P5 makes this honour the global `settings.backend`);
consumer contract (block id, library, mount) unchanged (W0-P0 table).

## Out of scope

- Implementing Mock/DB (P2/P3). P0 ships the **mechanism**; Solr stays the only
  real backend until P1 extracts it behind the plugin.
- Vue side. PHP only.

## Result

Shipped (PR #4). `ActivityFinderBackend` plugin type — `DefaultPluginManager` +
`#[ActivityFinderBackend]` attribute (annotation fallback) + base, discovered
from `src/Plugin/ActivityFinderBackend/` (mirrors core `ArchiverManager`). The
manager is the **factory** for all three apps. Per-block `backend_plugin`
single-select in `ActivityFinder4Block` (covers block, paragraph, LB — one
`blockForm`); a block with none chosen inherits the global default. Backend id
forwarded to the JS via the twig `:backend` prop and back as a `backend[]`
query, validated against the registry (D8). Consumer contract intact. Note: the
provisional default became **mock** (not solr) per the redesign — see DECISIONS.
