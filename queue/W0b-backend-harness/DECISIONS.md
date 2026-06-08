# W0b Decisions — backend plugin system

Locked choices for the backend plugin type. Cite these from the phases; do not
re-litigate mid-stream.

## D1 — Discovery mechanism: Drupal plugin system (not hand-rolled)

- **Decision:** Use the **Drupal plugin API** for discovery — a
  `DefaultPluginManager`, PHP **attribute** `#[ActivityFinderBackend(...)]`
  (annotation fallback on cores without attribute discovery), plugin
  definitions auto-discovered from `src/Plugin/ActivityFinderBackend/`.
- **Why:** Drupal already provides discovery, caching, derivatives, alter
  hooks. Hand-rolling an annotation reader duplicates core for no gain. The
  backend *contract* (`OpenyActivityFinderBackendInterface`) is plain PHP and
  CMS-agnostic; only the **discovery host** is Drupal. A different CMS would
  supply its own discovery against the same interface.

## D2 — Selection: per-block config, default Solr

- **Decision:** The backend is chosen in the **block config form**
  (`ActivityFinder4Block::blockForm` / `blockSubmit`) — a `select` listing
  discovered plugins, stored under a block config key (e.g. `backend_plugin`),
  resolved at render via the plugin manager. **Default = `solr`.**
- **Why:** Per-block selection lets one site run Mock on a dev block and Solr
  in production without code changes. Defaulting to Solr keeps every existing
  site byte-identical until an operator opts in. No global config switch — the
  choice lives with the block instance that renders AF4.

## D3 — Plugin id scheme

- **Decision:** ids `solr`, `mock`, `db`. The extracted current backend keeps
  behaviour and takes id **`solr`** (the default), so existing blocks resolve
  to it with no migration of stored config.

## D4 — Mock before DB

- **Decision:** Build **Mock (P2) before DB (P3).**
- **Why:** Mock is the migration unblocker — fixtures, zero infra, deterministic
  screens for W0-P1 baseline and Ira's QA. It ships first so W1+ can start. DB
  (real entity queries without Solr) is more work and follows; it is not on the
  critical path to starting the migration.

## D5 — No new search semantics

- **Decision:** Every plugin implements the **existing**
  `OpenyActivityFinderBackendInterface` faithfully. Mock returns fixture data
  shaped like Solr results; DB reproduces the same result contract via entity
  queries. No backend invents filters, sorts, or fields the interface does not
  already define.
