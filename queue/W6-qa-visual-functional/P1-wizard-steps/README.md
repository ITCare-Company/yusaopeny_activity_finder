# P1 — QA: Wizard steps

## Goal

Verify all 7 wizard step screens match baseline + behave on Vue 3.

## Components (Ira QA table rows)

`steps/SelectActivities.vue`, `SelectAges.vue`, `SelectDays.vue`,
`SelectDaysTimes.vue`, `SelectLocations.vue`, `SelectTimes.vue`,
`SelectWeeks.vue`.

## Steps

1. For each step, drive the wizard to it, capture + diff vs baseline.
2. Functional: each step's selection updates `App` state and advances; the
   `SelectAges` step (BootstrapVue-migrated in W4-P3) gets extra attention on
   its control behaviour.
3. Update `verified` / `Note` per row.

## Tests

Visual diff + selection smoke per step.

## Validation

Owner + Ira approve. All 7 step rows `verified` or noted. Any mismatch on
`SelectAges` routed back to W4-P3.

## Out of scope

Other groups; responsive sweep (P5).

## Result

(to be filled when phase ships)
