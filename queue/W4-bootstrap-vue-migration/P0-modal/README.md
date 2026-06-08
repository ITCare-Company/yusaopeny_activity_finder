# P0 — Replace BootstrapVue in `modals/Modal.vue`

## Goal

Replace the BootstrapVue modal (`b-modal` / `$bvModal`) in the base modal
shell with the W1-P1 choice, preserving the open/close API the other modals
rely on and the rendered markup/classes.

## Files

- `openy_af4_vue_app/src/components/modals/Modal.vue`
- Consumers that open it: `modals/ActivityDetails.vue`, `modals/Filters.vue`,
  `modals/BookmarkFeature.vue`, `modals/BookmarkFeatureDescription.vue`,
  `modals/BookmarkedItems.vue` — confirm how each triggers the shell (slot,
  prop, method) but **do not** rewrite their bodies here unless the trigger API
  changes.

## Steps

1. Read `Modal.vue`; enumerate the `b-*` API surface used (`b-modal` props,
   `$bvModal.show/hide`, events, scoped slots).
2. Implement the W1-P1 replacement (bootstrap-vue-next `BModal`, plain BS5
   modal markup + small controller, or hand-rolled) keeping the same:
   - public props/events the consumers call,
   - slot names,
   - root classes (so baseline matches).
3. If the trigger API must change, update the 5 consumers minimally and note
   the API delta in W4 `DECISIONS.md`.
4. Verify show/hide, backdrop, escape-to-close, focus behaviour match baseline.

## Tests

```sh
cd openy_af4_vue_app && npm run lint && npm run build
```

Harness: open each modal that uses the shell; confirm open/close + markup vs
W0 baseline.

## Validation

Owner approves. Modal opens/closes identically; consumers unbroken; markup
matches baseline; any trigger-API change is documented.

## Out of scope

- Modal *content* rewrites (those ride along in W6 QA if needed).
- Other BootstrapVue consumers (P1–P4).

## Result

(to be filled when phase ships)

`Modal.vue` off BootstrapVue; consumer triggers verified; baseline parity
confirmed; API deltas (if any) in W4 `DECISIONS.md`.
