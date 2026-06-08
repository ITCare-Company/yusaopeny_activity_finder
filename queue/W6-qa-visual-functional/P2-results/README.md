# P2 — QA: Results

## Goal

Verify the results screen + states match baseline + behave on Vue 3.

## Components (Ira QA table rows)

`components/Results.vue`, `ResultsList.vue`, `ResultsBar.vue`,
`AvailableSpots.vue`, `NoResults.vue`, `Loading.vue`.

## Steps

1. Run a search that returns results; capture + diff `Results`, `ResultsList`,
   `ResultsBar`, `AvailableSpots`.
2. Run a search with **no** results → `NoResults`; trigger a slow fetch →
   `Loading`. Capture + diff both states.
3. Functional: `ResultsBar` (BootstrapVue-migrated W4-P4) search + filter
   scoped slots + `hideModal`; pluralized result count (filter→method, W5-P0).
4. Update `verified` / `Note` per row.

## Tests

Visual diff across result/empty/loading states + bar interactions.

## Validation

Owner + Ira approve. All 6 rows `verified` or noted. `ResultsBar` mismatch →
W4-P4; count-format mismatch → W5-P0.

## Out of scope

Modals opened from results (P3); responsive sweep (P5).

## Result

(to be filled when phase ships)
