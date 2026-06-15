# P0 — QA: Entry + SelectPath

## Goal

Verify the entry render and path-chooser match baseline on Vue 3.

## Components (Ira QA table rows)

`App.vue`, `steps/SelectPath.vue`, `components/WizardBar.vue`,
`components/steps/Step.vue`.

## Steps

1. Load AF4 (Vue 3). Confirm `#activity-finder-app` renders and the entry
   screen (`SelectPath`) shows.
2. Capture + diff each component vs `../../W0-baseline-contract/P1-behavioral-baseline/baseline/`.
3. Functional checks: WizardBar emits `startOver` / `viewResults`; the error
   alert path (`data.error`) still renders; `Step` slot wrapper renders.
4. Update `verified` / `Note` in the INDEX table + `inventory.tsv`.

## URL query state checks (PR #11 fix — verify these specifically)

The `$route`/`$router` → native History API fix changed how URL state works.
Test all three scenarios:

| Scenario | Steps | Expected |
|---|---|---|
| Deep link restore | Open `?step=selectAges&selectedAges=6` directly | Correct step shown, age pre-selected |
| URL updates on interact | Start fresh, select a path/age | Address bar updates immediately |
| Reload preserves state | Get to results with filters, press F5 | Same filters and step after reload |
| Back button | Step through wizard 3+ steps, press Back | Previous step + its selections restored |
| Back to page start | Press Back from first AF step | Browser leaves AF page (no stuck loop) |

## Tests

Visual diff + interaction smoke (start a path, confirm wizard advances).

## Validation

Owner + Ira approve. All four rows `verified` or noted. URL state scenarios all pass.

## Out of scope

Other groups (P1–P4); responsive sweep (P5, single viewport here).

## Result

(to be filled when phase ships)
