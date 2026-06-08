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

## Backend note for QA (Ira)

The block's **limit / exclude** restrictions (limit/exclude by category and by
location, set in the block config) behave **per backend**:

- **Mock backend:** limit/exclude do **NOT** work. Mock serves static fixtures
  and the limit/exclude fields hold real node ids that the fixtures don't carry,
  so these restrictions have no effect. This is expected — do not log it as a
  bug on a Mock-backed block.
- **Solr backend (when configured):** limit/exclude **DO** work.

Все інше (вибір віку, локацій, днів, пошук) працює на обох бекендах. Щоб
перевірити limit/exclude — тестуй на блоці з backend = Solr.

To see which backend a page uses: the `<activity-finder>` element's `:backend`
attribute, or the `backend[]` query on `/af/get-data`, or a result row's
`backend` field.

## Tests

Visual diff per filter + apply/clear smoke.

## Validation

Owner + Ira approve. All 16 rows `verified` or noted.

## Out of scope

Responsive sweep (P5).

## Result

(to be filled when phase ships)
