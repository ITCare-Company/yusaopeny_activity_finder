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
  already define. The exact shape is the **response schema** in **D10**.

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

## D10 — Documented backend **response** schema + `externals` field (in the response)

- **Decision:** Formalize the **output contract** every backend plugin returns.
  Today the response shape is **implicit** (whatever the Solr backend happens to
  produce). W0b documents it explicitly and makes it the contract Mock/DB must
  satisfy. The canonical `runProgramSearch` response is: `count`, `facets`,
  `pager`, `pager_info`, `table`, `groupedLocations`, `sort`, `error` (failure
  only) — full field table in **MIGRATION-REFERENCE §10 → "Backend response
  schema"**, measured from `OpenyActivityFinderSolrBackend`.
- **`externals` is a field in the RESPONSE** (not block config): an **open
  key-value map** a backend adds to its returned data for **backend-specific**
  values that do not fit the shared schema (e.g. Daxko-only fields). Common
  consumers ignore it; bespoke consumers read it.
- **Why:** with a plugin system — and a block that may run **more than one**
  backend — the Vue app and any aggregation need a **single uniform shape**
  across backends. `externals` absorbs per-backend differences so nobody adds
  divergent top-level keys (which would break aggregation and the consumer
  contract). The shared schema stays stable; extension happens in `externals`.
- **W0b-P1** records the exact shape from Solr (the reference); **P2/P3** must
  emit it, putting any extras only under `externals`.

## D11 — Multiple backends per block + aggregation (locked-pending)

- **Decision (shape):** the design must allow a block to run **one or more**
  backends. Each returns the **D10** response schema; per-backend extras go in
  `externals`. A single-backend block is the default (back-compatible).
- **Decision (aggregation): LOCKED-PENDING — owner (Vlad) to choose.** How N
  backend responses combine into one is **not yet locked**. Candidate rules:
  (a) concat by weight + dedup by entity id, facets/pager from the primary
  (first by weight); (b) full merge of results + facets + pager; (c) primary +
  fallback (no aggregation, switch on empty/down). Default leaning is **(a)** as
  the simplest predictable v1; do **not** implement merge logic until this is
  locked. Until then, ship the **uniform response schema + `externals`** (D10),
  which every option needs.
- **Why split:** the response schema (D10) is needed regardless and unblocks the
  plugins; the aggregation rule is a product decision with real complexity
  (cross-backend facet normalization, global pagination) and is separable.

## Redesign update — owner decisions (implemented in commit 5df646c)

The owner reshaped the backend design while building it. The following
supersede the decisions above; cite this section, not the originals.

### D2/D3 superseded — default is **mock**, selection is a **list**

- Default backend is **`mock`**, not `solr`. New installs run AF4 with no Solr.
- The per-block key `backend_plugin` holds a **list** of plugin ids (checkboxes
  in `ActivityFinder4Block::blockForm`), primary first — a block may run one or
  more backends (D11).
- Existing sites must therefore be migrated to keep Solr (their blocks have no
  stored `backend_plugin` and would resolve to the mock default) — see
  "existing-site migration" task; this is the D7/P5 work, still deferred.

### D10 superseded — `runProgramSearch` is **decomposed** in the contract

- `OpenyActivityFinderBackendInterface` no longer declares `runProgramSearch()`.
  Each plugin implements **`getResultsCount(params)`**, **`getFacets(params)`**
  (cheap, rows-free) and **`getResults(params, offset, limit, log_id)`** (a
  slice). The `runProgramSearch` **response shape** is unchanged and is now
  assembled in `ActivityFinderBackendAggregator`. JSON Schema:
  `fixtures/schema/runProgramSearch.schema.json`.
- `OpenyActivityFinderBackend` no longer implements the interface; the `solr`
  plugin wraps the `OpenyActivityFinderSolrBackend` service.

### D11 locked — aggregation = **count-offset routing** (not concat)

- count = sum of per-backend `getResultsCount`; facets = merged per filter
  (counts summed); results = the global page is **routed** across backends using
  their counts so only the needed slice is fetched (no full-set reads, no silent
  fallback). The earlier concat/"primary facets" option was rejected (broke
  pagination).

### Backend id transport — per-block via the template, validated against the registry

- The block forwards its selected ids to the JS through the twig `:backend`
  prop (per-block, so several AF blocks on one page stay distinct — **not** a
  global `drupalSettings`). The JS sends them back as a `backend[]` query; the
  controller keeps only ids present in `getDefinitions()` and the aggregator
  returns an explicit error when none resolve. No silent default substitution.

### New — Solr moves to a submodule

- `OpenyActivityFinderSolrBackend` + the `solr` plugin + the `search_api_solr`
  dependency move to `openy_activity_finder_solr`, so the main module does not
  depend on Solr. (Task pending.)

### RULES exception — W0b touched the Vue app

- W0b is "PHP-only" in RULES, but forwarding the per-block backend required a
  minimal `openy_af4_vue_app/src` change (App.vue `backend` prop + the twig
  prop) and a `dist/` rebuild. This is a deliberate, scoped exception recorded
  here.

