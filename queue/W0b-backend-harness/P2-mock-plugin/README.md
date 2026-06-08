# P2 — Mock backend plugin (the migration unblocker)

## Goal

A backend plugin that serves AF4 from **static fixtures** — **zero Solr, zero
DB**. Pick it on a block and AF4 renders full screens (locations, filters,
sorts, ages, program results, session details) on any local/CI box with no
search infrastructure. This is what lets the Vue 2 → Vue 3 migration (W1+)
start and iterate.

> **Scope of "unblock".** Mock unblocks **running and developing** AF4 without
> Solr. It does **not** enable automated tests — the current AF4 markup is
> non-semantic (RULES "Semantic markup"), so meaningful keyboard/a11y/DOM tests
> are not writable until the migration redoes the markup. QA stays manual (Ira)
> through W6.

## Files

- New: `src/Plugin/ActivityFinderBackend/MockBackend.php` —
  `#[ActivityFinderBackend(id: 'mock', label: 'Mock (fixtures, no Solr)')]`,
  extends the P0 base, implements every `OpenyActivityFinderBackendInterface`
  method from fixtures.
- New: `src/Plugin/ActivityFinderBackend/fixtures/*.php|*.json` — fixture data
  shaped **exactly** like the Solr backend's return contract (same keys for
  results, facets, pager, locations, sort options, ages).

## Steps

1. Capture the real return shape of each interface method from the Solr backend
   (record one real response per method as the fixture template — no invented
   fields, per D5).
2. Implement `MockBackend` returning those fixtures: `runProgramSearch`
   (results + facets + pager honouring the basic filter/sort params),
   `getLocations`, `getSortOptions`, `getRelevanceSort`, `getAges`, and the
   rest of the interface.
3. Make fixtures cover enough breadth for W0-P1 baseline: multiple locations,
   several programs, a result set that paginates, a session-detail payload, an
   empty-result case (NoResults screen).
4. Select `mock` on a dev AF4 block; walk the wizard end-to-end with **no Solr
   container running**.

## Tests

```sh
# No Solr. Fresh site, AF4 block backend = mock.
ddev drush cr
# Open AF4, walk: SelectPath → steps → results → result detail → NoResults.
# Every screen renders from fixtures; no Solr/DB query fires.
```

## Validation

Owner approves. With Solr **stopped**, AF4 renders every screen from fixtures;
fixture shapes match the Solr contract (D5); the empty-result path renders
NoResults. After this phase, **migration W1+ is unblocked**.

## Out of scope

- Real data (DB — P3).
- Automated tests (future, post-migration — markup-blocked).
- Any Vue change.

## Result

(to be filled when phase ships)

Mock backend ships; AF4 runs with no Solr; fixtures cover the baseline screens;
migration unblocked.
