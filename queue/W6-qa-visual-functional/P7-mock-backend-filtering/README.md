# P7 — QA: MockBackend filtering (PR #12)

## What was fixed (PR #12)

| # | Bug | Root cause | Fix |
|---|-----|------------|-----|
| 1 | Age filter had no effect on results | `filterRows()` did not read `ages` param | Added `filterByAge()` in MockBackend |
| 2 | selectActivities showed 0 results for all categories | `getCategories.json` had old IDs (34–55); fixture uses new IDs (73, 80, 85) | Synced `getCategories.json` to sandbox IDs (70–91) |
| 3 | selectLocations showed 0 counts, all options disabled | `getLocations.json` had old IDs (25–27); Vue matches by `facet.id`; no match → count 0 | Synced `getLocations.json` to sandbox IDs (60–63) |
| 4 | Location counts did not update after other filters applied | `getFacets()` served static fixture counts for locations | Added locations facet recompute from filtered rows |
| 5 | selectDaysTimes header showed total (62) not filtered count | `af_weekdays_parts_of_day` facets not recomputed after filtering | Added `recomputeWeekdaysPartsOfDay()` in `getFacets()` |
| 6 | Day+time slot filter had no effect on results page | `filterRows()` did not read `daystimes` param | Added `filterByDaysTimes()` with slot-overlap logic |
| 7 | URL params (`?step=…&selectedAges=…`) lost on page reload | Vue Router was removed; no History API replacement | Cherry-picked PR #11 (native History API + popstate) |

---

## Test site

**URL base:** https://af4-migration.ddev.site/activity-finder-v4-layout-builder
**Backend:** Mock (31 static sessions, no Solr)

---

## Test scenarios

### T1 — URL state persists on reload

| Step | Action | Expected |
|------|--------|----------|
| T1.1 | Open `?step=results&selectedAges=6` | Page loads on results step, age filter pre-selected |
| T1.2 | Reload the page (Cmd+R) | Same step and filters stay — not reset to start |
| T1.3 | Open `?step=selectDaysTimes&selectedAges=6&selectedActivities=73` | Lands on selectDaysTimes step with ages+activities already selected |
| T1.4 | Use browser Back button after navigating steps | Returns to previous step with its filters |

---

### T2 — Age filter

| Step | URL | Expected count |
|------|-----|---------------|
| T2.1 | `?step=results&selectedAges=6` | 31 results (age 6 months matches all sessions) |
| T2.2 | `?step=results&selectedAges=24` | 31 results (age 24 months = 2 yr, matches all) |
| T2.3 | `?step=results&selectedAges=216` | Fewer results — only adult sessions (18 yr+) |

---

### T3 — selectActivities step

| Step | URL | Expected |
|------|-----|----------|
| T3.1 | `?step=selectActivities&selectedAges=24` | 3 category groups visible with non-zero counts |
| T3.2 | (same) | Swimming: 25, Health and Fitness: 5, Kids and Family Activities: 1 |
| T3.3 | (same) | Other groups (Camps, Child Care, Youth Programs) hidden or count=0 |

---

### T4 — selectLocations step

| Step | URL | Expected |
|------|-----|----------|
| T4.1 | `?step=selectLocations&selectedAges=24` | Downtown YMCA: 16, West YMCA: 15 shown and enabled |
| T4.2 | `?step=selectLocations&selectedAges=24&selectedActivities=73` | Counts update — only sessions in category 73 (5 total) split by location |
| T4.3 | `?step=selectLocations&selectedAges=24&selectedActivities=85` | Counts update — only sessions in category 85 (25 total) split by location |

---

### T5 — selectDaysTimes step: header counts

| Step | URL | Expected |
|------|-----|----------|
| T5.1 | `?step=selectDaysTimes&selectedAges=6` | Each day header shows correct count (not 62) |
| T5.2 | `?step=selectDaysTimes&selectedAges=6&selectedActivities=37` | Day counts reduce to match filtered sessions only |
| T5.3 | Monday header | Shows sum of Monday Morning + Afternoon + Evening counts |
| T5.4 | Monday → Evening slot | Shows count ≥ 1 (at least 2 sessions on Monday evenings) |

---

### T6 — Day+time slot filtering on results

| Step | URL | Expected |
|------|-----|----------|
| T6.1 | `?step=results&selectedAges=6&selectedDaysTimes=13` | 2 results (Monday Evening sessions only) |
| T6.2 | `?step=results&selectedAges=6&selectedDaysTimes=10` | All Monday sessions (any time) |
| T6.3 | `?step=results&selectedAges=6&selectedDaysTimes=11` | Monday Morning sessions only |
| T6.4 | `?step=results&selectedAges=6&selectedDaysTimes=13,23` | Monday Evening + Tuesday Evening combined |

---

### T7 — Combined filters

| Step | URL | Expected |
|------|-----|----------|
| T7.1 | `?step=results&selectedAges=6&selectedActivities=85&selectedDaysTimes=13` | Swim Lessons on Monday Evening only |
| T7.2 | `?step=results&selectedAges=6&selectedLocations=61` | Results from Downtown YMCA only |
| T7.3 | `?step=results&selectedAges=6&selectedActivities=85&selectedLocations=60` | Swim Lessons at West YMCA only |

---

### T8 — Wizard flow (full walk-through)

| Step | Action | Expected |
|------|--------|----------|
| T8.1 | Start at `/activity-finder-v4-layout-builder` | selectAges step shown |
| T8.2 | Select age → Next | Advances to selectActivities |
| T8.3 | Select Swimming → Next | Advances to selectLocations with filtered counts |
| T8.4 | Select Downtown YMCA → Next | Advances to selectDaysTimes |
| T8.5 | Select Monday Evening → Next | Advances to results showing matching sessions only |
| T8.6 | URL in address bar | Contains all selected values as query params |
| T8.7 | Reload page | Returns to results step with same filters applied |

---

## Pass criteria

- All T1 scenarios: URL params persist through reload and browser Back
- T3.2: counts exactly Swimming=25, Health and Fitness=5, Kids=1
- T6.1: exactly 2 results for Monday Evening + age 6
- T7–T8: combined filters narrow results correctly
- No "62 results" in any day header on selectDaysTimes

## Result

(to be filled when phase ships)
