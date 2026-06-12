# P5 — BUG: MockBackend age filter missing

**Found during:** PR #11 testing (URL query state fix).
**Fixed in:** PR #12.
**Status:** FIXED.

## Symptom

On the `selectAges` wizard step, selecting age 6 (6 months) showed a facet
count of **3** for that age group. Clicking Next to `selectActivities`
displayed **all 31 sessions** — as if no age filter was applied.

## Root cause

`MockBackend::filterRows()` filtered sessions by location, category, keyword,
and days — but completely ignored the `ages` parameter. Every request returned
all 31 sessions regardless of selection.

The fixture rows carry `min_age` / `max_age` fields in months. No `filterByAge`
method existed; the comment in the class said "best-effort, not full Solr
parity" and age was not considered worth implementing.

## How we found it

After fixing `$route`/`$router` → native History API (PR #11), URL params
became properly observable. Navigating to
`?step=selectActivities&selectedAges=6` made the mismatch visible: facet said
3 but backend returned 31.

## Fix

Added `filterByAge(array $rows, string $csv): array` to `MockBackend`.
Keeps rows where at least one selected age (months) satisfies:

```
min_age <= selected_age <= max_age
```

Empty `ages` param passes all rows through — matches existing pattern.

## Verification

```
ages= (none)  → 31 sessions
ages=6        → 3 sessions  (6-month-old programs)
ages=216      → 15 sessions (18+ year programs)
```

## Result

FIXED. PR #12 merged into 7.x.
