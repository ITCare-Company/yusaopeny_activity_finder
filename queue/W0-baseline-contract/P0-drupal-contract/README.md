# P0 — Drupal consumer contract inventory

## Goal

Record the exact boundary between Drupal and the AF4 bundle, so the migration
can change everything inside `openy_af4_vue_app/src/` while keeping the
consumer side byte-stable. This table is the acceptance contract for W7.

## Files

Read-only. Output: `findings.md` in this phase folder.

Sources:
- `openy_activity_finder.libraries.yml` (the `activity_finder_4` library, ~line 62).
- `openy_activity_finder.module`, `.routing.yml`, `.links.menu.yml`.
- `templates/` — any Twig that renders the `#activity-finder` mount.
- `openy_af4_vue_app/package.json` (build `--name activity_finder_4`).
- `openy_af4_vue_app/src/main.js` (`$mount('#activity-finder')`).
- `openy_af4_vue_app/src/App.vue` (`#activity-finder-app` root).
- `openy_af4_vue_app/src/client/index.js` (`window.drupalSettings.path.baseUrl`).

## Steps

1. Tabulate the contract points (library id, JS path, CSS path, UMD global,
   mount id, root render id, runtime globals). Start from §7 of
   `MIGRATION-REFERENCE.md` and **verify each row against the actual files**.
2. Trace how the bundle is attached: which render array / preprocess / block
   attaches `activity_finder_4`, and what `drupalSettings` keys AF4 reads at
   runtime (`path.baseUrl`, anything else under `window.drupalSettings`).
3. Grep templates for `activity-finder` to confirm the mount markup and that
   no inline data is passed except via `drupalSettings`.
4. Record the API endpoints AF4 calls (`af/get-data`, `af/api/v1/session-data`,
   `af/more-info` from `client/index.js`) — confirms backend contract is
   untouched by this migration.

## Tests

Research phase — no automated test. Exit criterion: `findings.md` lets W7
verify the produced build against a fixed checklist without re-deriving it.

## Validation

Owner-review checkpoint. Show `findings.md`. Confirm every contract row has a
file+line anchor and that nothing on the Drupal side must change for the
migration (or, if something must, it is flagged here, not discovered in W7).

## Out of scope

- Changing any contract point — recording only.
- AF3 / Camp Finder libraries.

## Result

DONE 2026-06-09. `findings.md` shipped.

Contract verified on 7.x branch (Mock backend, af4-migration.ddev.site):
- Library ID: `activity_finder_4`, bundle: `openy_af4_vue_app/dist/activity_finder_4.umd.min.js`
- Mount: `<div id="activity-finder">`, root component: `<activity-finder>`
- Vue + BootstrapVue externalized (CDN/openy_system) — NOT bundled
- 30 props passed via JSON-encoded twig bindings
- 3 API endpoints: `/af/get-data`, `/af/api/v1/session-data`, `/af/more-info`

Drupal-side changes flagged for W7:
- Replace `openy_system/vue` → vue3 (or bundle) after W3
- Remove `bootstrap-vue` + `popper` library entries after W4
- `:bs-version` prop cleanup optional
