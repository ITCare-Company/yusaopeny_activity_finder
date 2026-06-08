# P1 — BootstrapVue replacement decision

## Goal

Decide how to replace BootstrapVue (no Vue 3 build exists) across the 7 AF4
consumers, choosing the **smallest surface that preserves the existing
markup/classes** so the W0 visual baseline still matches.

## Files

Read-only research. Output: decision in [`../DECISIONS.md`](../DECISIONS.md)
plus a per-file replacement note feeding W4 phase scoping.

Consumers (from `MIGRATION-REFERENCE.md` §4):
- `src/components/modals/Modal.vue` (core modal shell)
- `src/components/Fieldset.vue`
- `src/components/Foldable.vue`, `src/components/FoldableInput.vue`
- `src/components/filters/Ages.vue`, `src/components/steps/SelectAges.vue`
- `src/components/ResultsBar.vue`

## Steps

1. For each consumer, enumerate the exact `b-*` components / `v-b-*`
   directives / `$bvModal` calls actually used (use the W0-P2 audit notes).
2. Evaluate options:
   - **`bootstrap-vue-next`** — Vue 3 port; closest API to current `b-*`
     markup, but still pre-1.0 (API churn risk). Lowest rewrite cost if the
     used components are covered.
   - **Plain Bootstrap 5 markup + tiny local components** — drop the `b-*`
     wrapper, render Bootstrap classes directly, hand-roll the few behaviours
     (modal open/close, collapse). Most control, preserves CSS classes, no
     new dep churn.
   - **Hand-rolled replacements** — for the handful of widgets (modal,
     collapse, form bits) write small AF4-local components.
3. Decision criterion (in priority order): (a) keep the rendered markup/classes
   so baseline screenshots match, (b) minimise new dependency risk, (c)
   minimise rewrite surface.
4. Recommend per-file (a single strategy may not fit all 7 — e.g.
   bootstrap-vue-next for modal, plain markup for `Fieldset`).

## Tests

No code. May prototype one widget (e.g. modal) to validate the chosen
approach reproduces the baseline markup.

## Validation

Owner approves. Decision names the strategy per consumer and confirms the
chosen approach keeps the W0 baseline visually identical.

## Out of scope

- Performing the replacement (W4).
- Restyling — this is a like-for-like replacement, not a redesign.

## Result

(to be filled when phase ships)

Replacement strategy (possibly per-file) recorded in W1 `DECISIONS.md`; W4
phase scoping updated to match.
