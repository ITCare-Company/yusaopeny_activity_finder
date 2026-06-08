# P3 — QA: Modals

## Goal

Verify all 6 modals open/close + render correctly on Vue 3 (BootstrapVue
replaced in W4-P0).

## Components (Ira QA table rows)

`modals/Modal.vue` (shell), `ActivityDetails.vue`, `Filters.vue`,
`BookmarkFeature.vue`, `BookmarkFeatureDescription.vue`, `BookmarkedItems.vue`.

## Steps

1. Open each modal from its trigger (result "More info" → `ActivityDetails`;
   results filter → `Filters`; bookmark flows → the three bookmark modals).
2. Capture + diff each open state vs baseline; verify backdrop, escape-close,
   focus trap, scroll behaviour.
3. Functional: `BookmarkedItems` slot content; `Filters` modal applies filters
   to results.
4. Update `verified` / `Note` per row. Any modal-shell regression → W4-P0.

## Tests

Visual diff per modal + open/close/focus smoke.

## Validation

Owner + Ira approve. All 6 rows `verified` or noted.

## Out of scope

Filter bodies inside the Filters modal (P4); responsive sweep (P5).

## Result

(to be filled when phase ships)
