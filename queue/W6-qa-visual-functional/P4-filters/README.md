# P4 — QA: Filters

## Goal

Verify all 16 filter components render + behave on Vue 3.

## Components (Ira QA table rows)

`filters/Filters.vue`, `Activities.vue`, `Ages.vue`, `Days.vue`,
`DaysTimes.vue`, `Times.vue`, `Weeks.vue`, `Durations.vue`, `StartMonths.vue`,
`Locations.vue`, `InMemberships.vue`, `SearchForm.vue`, `SortSelect.vue`,
`SortRadios.vue`, `Pager.vue`, `DaxkoPager.vue`.

## Steps

1. Open the filters panel; for each filter, capture + diff vs baseline.
2. Functional: each filter updates results; `SearchForm` `v-model` →
   `searchKeywords` (W5-P2); `Ages` control (W4-P3); sort + pager change
   ordering/page; `DaxkoPager` only on Daxko-source data.
3. Update `verified` / `Note` per row. `Ages` mismatch → W4-P3; `SearchForm`
   v-model mismatch → W5-P2.

## Tests

Visual diff per filter + apply/clear smoke.

## Validation

Owner + Ira approve. All 16 rows `verified` or noted.

## Out of scope

Responsive sweep (P5).

## Result

(to be filled when phase ships)
