# Follow-ups — W0b Backend plugin system & local-dev harness

Scope-discipline parking lot (per queue anatomy: keep the wave lean, capture
ideas here, cite from a future wave instead of widening this one).

## Open items

- **Multi-backend aggregation.** Run more than one backend per block and merge
  the results. Candidate rules: count-offset routing / primary→fallback / full
  merge — pick one, test every path, do a quality merge (facets, pager, dedup).
  Driver: combine hand-edited content with API-sourced content (more weight on
  manual edits). First confirm it is actually needed.
- **Plugin-driven limit/exclude selectors.** The block's `limit_by_category`,
  `exclude_by_category`, `limit_by_location`, `exclude_by_location` are
  `entity_autocomplete` on real nodes, so they only line up with backends that
  use Drupal node ids (Solr, DB). For Mock — whose whole point is running with
  **no Solr/PEF and no real content** — the configured node ids need not exist,
  so these restrictions can't be expressed. Extend the selectors to be
  **plugin-driven**: options come from the selected backend(s) (each plugin
  advertises its filterable categories/locations), so limit/exclude work per
  backend including Mock. Small, separable task — do later. Until then Mock
  honours limit/exclude only where its fixture ids happen to match the stored
  node ids (i.e. on the site the fixtures were captured from).
- **DB backend** plugin (entity/database query, no Solr).
- **Legacy per-block config mapping (P5)** — full service-id→plugin-id map +
  per-block config stamping.
