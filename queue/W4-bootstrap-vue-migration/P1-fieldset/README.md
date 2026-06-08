# P1 — Replace BootstrapVue in `Fieldset.vue`

## Goal

Replace the BootstrapVue form/layout widget(s) in `Fieldset.vue` with the
W1-P1 choice, preserving markup/classes and the props/slots consumers pass.

## Files

- `openy_af4_vue_app/src/components/Fieldset.vue`
- Note its consumers (filters/steps reuse `Fieldset`) — confirm prop/slot
  contract unchanged.

## Steps

1. Enumerate the `b-*` usage in `Fieldset.vue`.
2. Implement the replacement, keeping public props, slots, and root classes.
3. Confirm every consumer still renders identically (Fieldset is reused widely;
   a class change cascades).

## Tests

```sh
cd openy_af4_vue_app && npm run lint && npm run build
```

Harness: render a filter and a step that use `Fieldset`; compare to baseline.

## Validation

Owner approves. Markup/classes preserved; consumers unbroken.

## Out of scope

- Other consumers (P0, P2–P4).

## Result

(to be filled when phase ships)

`Fieldset.vue` off BootstrapVue; reuse sites verified vs baseline.
