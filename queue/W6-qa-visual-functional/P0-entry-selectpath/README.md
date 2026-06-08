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

## Tests

Visual diff + interaction smoke (start a path, confirm wizard advances).

## Validation

Owner + Ira approve. All four rows `verified` or noted.

## Out of scope

Other groups (P1–P4); responsive sweep (P5, single viewport here).

## Result

(to be filled when phase ships)
