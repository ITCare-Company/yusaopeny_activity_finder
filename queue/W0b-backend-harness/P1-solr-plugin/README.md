# P1 — Extract Solr backend behind the plugin

## Goal

Move the existing Solr implementation behind the new plugin type as id `solr`,
with **zero behaviour change**. This proves the plugin seam against the real
backend before Mock/DB are added.

**Also records the canonical response schema (D10).** The Solr backend is the
reference shape every other plugin must emit. As part of this phase, document
the exact `runProgramSearch` response (and the other interface methods) in
MIGRATION-REFERENCE §10 from the real code, and add the new top-level
**`externals`** key to the response (empty for Solr) so Mock/DB have a defined
slot for backend-specific extras.

## Files

- `src/OpenyActivityFinderSolrBackend.php` → becomes (or is wrapped by) a
  plugin at `src/Plugin/ActivityFinderBackend/SolrBackend.php` with
  `#[ActivityFinderBackend(id: 'solr', label: 'Apache Solr')]`, extending the
  P0 base, keeping its current logic verbatim.
- `openy_activity_finder.services.yml` — the Solr service's dependencies
  (`config.factory`, `cache.default`, `database`, `entity_type.manager`,
  `date.formatter`, `datetime.time`, `logger.channel.default`,
  `module_handler`) move to the plugin's `create()` (container injection).
- Remove the legacy `openy_activity_finder.solr_backend` alias once consumers
  resolve via the manager.

## Steps

1. Wrap/move the Solr class into the plugin namespace; implement `create()`
   pulling the same services it has today.
2. Tag it `id: 'solr'` so default block config resolves to it unchanged.
3. Repoint any remaining direct references to the fixed service onto the
   manager.
4. Diff behaviour: same search results, same filters/locations/sorts/ages for
   the same query (Solr running).

## Tests

```sh
ddev drush cr
ddev drush php:eval '$m=\Drupal::service("plugin.manager.activity_finder_backend"); var_dump(array_keys($m->getDefinitions()));' # includes 'solr'
```

With Solr up + content indexed, run a search through AF4 — identical results to
pre-extraction (compare against a saved query response).

## Validation

Owner approves. `solr` plugin discovered and is the default; search output
byte-identical to the pre-plugin Solr backend; no consumer contract change.

## Out of scope

- Mock/DB (P2/P3).
- Changing Solr query logic — pure relocation.

## Result

(to be filled when phase ships)

Solr backend behind plugin id `solr`; default preserved; no behaviour drift.
