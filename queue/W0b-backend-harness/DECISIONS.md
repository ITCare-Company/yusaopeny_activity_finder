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
  in production without code changes. The new per-block key **supersedes** the
  legacy **global** `openy_activity_finder.settings.backend` service-id switch
  — so a block with no per-block value must fall back to that global setting,
  not a hardcoded Solr (otherwise non-Solr sites are silently flipped). That
  fallback is **D7 / P5**.

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

## D6 — Daxko backend: skip (module not enabled)

- **Decision:** Do **not** build a Daxko backend plugin in this wave. The
  `openy_daxko2` backend (`openy_daxko2.openy_activity_finder_backend`, which
  `ActivityFinder4Block` special-cases) only exists when that module is enabled;
  it is **not enabled** here, so it is out of scope.
- **Guard:** P5's fallback must **not** substitute Solr for an unmapped/legacy
  service-id (e.g. a Daxko backend on a site that does enable the module) — it
  keeps resolving via the old service-id path. No silent substitution
  (`feedback_no_fabricated_behavior`).

## D7 — Legacy global-config fallback is deferred (P5, Lera → Vlad)

- **Decision:** P0 ships a **provisional** literal `'solr'` default for blocks
  with no `backend_plugin`. The **correct** fallback — read the existing global
  `openy_activity_finder.settings.backend` and map it to the matching plugin id
  (optionally a one-time `hook_update_N` stamping per-block config) — is split
  into **P5** as **complex, deferred work**, likely a **Lera → Vlad** handoff.
- **Why split:** it is the hard part (config upgrade path + service-id↔plugin-id
  map + Daxko guard), separable from shipping the plugin mechanism. P5 must land
  **before** the legacy `settings.backend` service-id route is removed, so no
  site is flipped to Solr.

## D8 — Controller-to-Block Backend Synchronization Gap

- **Decision:** The block's chosen `backend_plugin` must be passed to the frontend JS app (e.g., via `drupalSettings` or a `data-backend` attribute on the `#activity-finder` mount element), and the JS client must include this plugin id as a query parameter in all AJAX requests (e.g. `?backend=mock`).
- **Why:** The AJAX endpoint `/af/get-data` has no block instance context. Without sending the block's chosen backend plugin id from the client, the controller will fall back to the site-wide default backend, breaking per-block isolation during active searches.

## D9 — Drupal 10 Attribute Discovery Compatibility

- **Decision:** The backend plugin type must natively support Attribute-based discovery (`#[ActivityFinderBackend(...)]`). If backwards compatibility with older Drupal core versions (under 10.2) is required, the plugin manager should provide an annotation discovery fallback.
- **Why:** The module's `core_version_requirement` is `^10 || ^11`. Attribute discovery was introduced in Drupal 10.2, so older 10.x installations lack native attribute discovery.

