# P3 — Legacy slot syntax sweep

## Goal

Ensure all 17 slot-using components use Vue 3-valid slot syntax. `v-slot` is
already compatible; this phase only fixes any legacy `slot=` / `slot-scope=`.

## Files

- The 17 slot consumers (from W0-P2): `App.vue`, `ResultsBar.vue`,
  `Results.vue`, `Fieldset.vue`, `Foldable.vue`, `FoldableInput.vue`,
  `modals/Modal.vue`, `modals/BookmarkedItems.vue`, and the `steps/Select*`
  + `Step.vue`.

## Steps

1. Grep for legacy syntax:
   ```sh
   grep -rnE "slot-scope=|[^-]slot=" src
   ```
   `v-slot` and `<slot>` (definition) are fine; only `slot="x"` /
   `slot-scope="x"` attribute syntax is removed in Vue 3.
2. Rewrite any hit to `v-slot:name="scope"`.
3. Confirm scoped-slot payloads (e.g. `ResultsBar` `{ hideModal }`,
   `App` `v-slot:search="{ hideModal }"`) still destructure correctly.

## Tests

```sh
cd openy_af4_vue_app && npm run lint && npm run build
grep -rnE "slot-scope=" src   # must return nothing
```

Harness: render slot-driven screens (results bar search/filter slots, modal
slots, foldables) vs baseline.

## Validation

Owner approves. No legacy slot syntax; scoped slots render identically.

## Out of scope

- emits / v-model (P2).

## Result

(to be filled when phase ships)

Slot syntax Vue 3-clean; scoped-slot payloads verified. (Likely a no-op if AF4
already uses `v-slot` throughout — confirm and record.)
