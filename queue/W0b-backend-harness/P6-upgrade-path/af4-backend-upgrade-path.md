# af4-backend-upgrade-path — Upgrade path (existing sites keep their backend)

## Goal

Make the move to the plugin backend automatic for existing sites: a site that
used the legacy global Solr service must keep using Solr after update, while
fresh installs default to Mock.

## Files

- `openy_activity_finder.install` — `hook_update_N` (`_9006`).

## Steps

1. Read the global `openy_activity_finder.settings:backend`.
2. Map the legacy **service id** to a **plugin id**:
   `openy_activity_finder.solr_backend` → `solr`,
   `openy_daxko2.openy_activity_finder_backend` → `daxko`.
3. When mapped to `solr`, enable the `openy_activity_finder_solr` submodule (it
   provides the `solr` plugin + its Search API config).
4. Save the setting as the plugin id. A value already a plugin id is left as is.

## Tests

- On a site with `backend = openy_activity_finder.solr_backend`, run `updb`: the
  submodule is enabled and `backend` becomes `solr`; Activity Finder still
  serves Solr results.
- A fresh install defaults to `mock` (config/install) — the update is a no-op.

## Validation

- `drush updb` reports the conversion; `/af/get-data?backend[]=solr` returns
  rows; no block loses its backend.

## Out of scope

- Per-block stamping of `backend_plugin` for embedded blocks (paragraph / LB /
  placed) and the full service-id↔plugin-id map for non-Solr/non-Daxko custom
  backends — that is the deferred legacy fallback (P5, Lera → Vlad).

## Result

Shipped (PR #4). `openy_activity_finder_update_9006()` converts the global
service id to a plugin id and enables `openy_activity_finder_solr` for Solr
sites. Custom-backend authors follow `UPGRADING.md`. Verified on a fresh
`small_y`: with the submodule enabled and demo sessions indexed,
`backend[]=solr` returns count 31; with it disabled, `backend[]=mock` returns
count 31.
