# P3 — DB backend plugin (real content, no Solr)

## Goal

A backend plugin that answers AF4 queries from **Drupal entities / the
database** directly — real content, **no Solr**. Lets a site (and dev/QA) run
AF4 on actual program/session nodes without operating a search cluster.

Returns the **response schema (D10 / MIGRATION-REFERENCE §10)** — same top-level
keys as Solr; any DB-only extras go under the response **`externals`** key. A
facet the DB cannot compute as cheaply is documented (not faked) and may live
in `externals` rather than degrading the shared shape.

> Not on the critical path to starting the migration (Mock already unblocks
> W1+). DB adds realism for QA and for small sites that do not want Solr.

## Files

- New: `src/Plugin/ActivityFinderBackend/DatabaseBackend.php` —
  `#[ActivityFinderBackend(id: 'db', label: 'Database (no Solr)')]`, extends the
  P0 base, implements `OpenyActivityFinderBackendInterface` via entity queries.

## Steps

1. Map each interface method to an entity/DB query over the same content the
   Solr index is built from (sessions, programs, locations, age taxonomy):
   `runProgramSearch` → entity query with filter/sort/pager;
   `getLocations`/`getSortOptions`/`getAges` → config + taxonomy/entity reads.
2. Reproduce the Solr **result contract** (D5): same result keys, facet shape,
   pager. Document any facet that DB cannot compute as cheaply, and how it is
   derived (or degraded) — no fabricated values.
3. Note performance posture: DB backend targets small/medium catalogues; large
   catalogues stay on Solr. Record the trade-off in this README's Result.
4. Select `db` on a block with demo content (W0b-P4) seeded; walk the wizard.

## Tests

```sh
# Solr stopped. Demo content seeded (W0b-P4). AF4 block backend = db.
ddev drush cr
# Walk the wizard; results come from entity queries; verify counts/filters
# match what the same content yields in Solr (cross-check a few queries).
```

## Validation

Owner approves. AF4 renders real seeded content with Solr stopped; result
contract matches Solr for the cross-checked queries; performance trade-off
documented.

## Out of scope

- Replacing Solr for large sites — DB is an option, not a mandate.
- Vue changes.

## Result

(to be filled when phase ships)

DB backend ships; AF4 runs on real content without Solr; contract parity +
performance posture documented.
