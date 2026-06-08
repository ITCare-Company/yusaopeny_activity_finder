# P2 — Replace BootstrapVue in `Foldable.vue` + `FoldableInput.vue`

## Goal

Replace the BootstrapVue collapse/toggle widgets in the two foldable
components, preserving the expand/collapse behaviour, ARIA, and markup.

## Files

- `openy_af4_vue_app/src/components/Foldable.vue`
- `openy_af4_vue_app/src/components/FoldableInput.vue`

## Steps

1. Enumerate the `b-*` collapse usage (`b-collapse` / `v-b-toggle` / etc.) in
   both files.
2. Implement the replacement (collapse via the chosen lib or a small
   `v-show`/transition controller), keeping:
   - the toggle trigger + animation,
   - ARIA attributes (`aria-expanded`, `aria-controls`),
   - root classes.
3. Both components share the foldable pattern — keep them consistent.

## Tests

```sh
cd openy_af4_vue_app && npm run lint && npm run build
```

Harness: expand/collapse a foldable filter section; confirm animation + ARIA +
markup vs baseline.

## Validation

Owner approves. Expand/collapse behaviour, ARIA, and markup match baseline.

## Out of scope

- Other consumers (P0, P1, P3, P4).

## Result

(to be filled when phase ships)

Both foldables off BootstrapVue; collapse behaviour + ARIA verified.
