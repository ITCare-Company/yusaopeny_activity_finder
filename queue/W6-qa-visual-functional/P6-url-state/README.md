# P6 — QA: URL query state (deep link, reload, back/forward)

## Goal

Verify that URL query state works correctly after the PR #11 fix
(native History API replacing vue-router).

## Components

`App.vue` — `getDataFromUrl()`, `updateUrl()`, `popstate` listener.

## Steps

| Scenario | How | Expected |
|---|---|---|
| Deep link restore | Open `?step=selectAges&selectedAges=6` directly | Correct step, age pre-selected |
| URL updates on interact | Start fresh, select path/age/filter | Address bar updates immediately |
| Reload preserves state | Apply filters, reach results, press F5 | Same filters + step after reload |
| Back button (1 step) | Step through wizard, press Back once | Previous step + its selections restored |
| Back button (multiple) | Step through 3+ steps, press Back repeatedly | Each Back restores correct prior state |
| Back to page start | Press Back from first AF step | Browser leaves AF page — no stuck loop |
| Forward button | Back then Forward | Correct state in both directions |

## Done when

All 7 scenarios pass. No regressions in P0–P5 visual/functional checks.

## Validation

Owner + Ira approve.

## Out of scope

Visual diff (P0–P5 cover that). Responsive (P5).

## Result

(to be filled when phase ships)
