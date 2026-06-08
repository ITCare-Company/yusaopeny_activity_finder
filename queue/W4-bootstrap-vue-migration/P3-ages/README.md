# P3 — Replace BootstrapVue in `filters/Ages.vue` + `steps/SelectAges.vue`

## Goal

Replace the BootstrapVue form control(s) in the two age components, preserving
the input behaviour, `v-model` binding, and markup. Grouped because both render
the age selector (filter view + wizard step view).

## Files

- `openy_af4_vue_app/src/components/filters/Ages.vue`
- `openy_af4_vue_app/src/components/steps/SelectAges.vue`

## Steps

1. Enumerate the `b-*` controls used (likely `b-form-*` / `b-form-checkbox`).
2. Implement the replacement keeping the `v-model` / emit contract and root
   classes. If the control change touches `v-model` semantics, coordinate with
   W5-P2 (emits/v-model audit) — note the dependency.
3. Verify selecting/deselecting ages updates state identically in both views.

## Tests

```sh
cd openy_af4_vue_app && npm run lint && npm run build
```

Harness: in the wizard age step and the results age filter, select ages and
confirm state + markup match baseline.

## Validation

Owner approves. Both age views behave + render identically to baseline.

## Out of scope

- Other consumers (P0–P2, P4).

## Result

(to be filled when phase ships)

Both age components off BootstrapVue; `v-model` parity verified.
