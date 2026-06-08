# P2 — Declare `emits` + audit `v-model`

## Goal

Bring the 44 components up to Vue 3 component-API rules: declare `emits` for
every custom event, and fix any `v-model` whose Vue 2 semantics changed.

## Files

- All `openy_af4_vue_app/src/**/*.vue` that `$emit` custom events or use
  `v-model`. Examples seen: `WizardBar` emits `startOver` / `viewResults`;
  `App` uses `v-model="searchKeywords"` on `SearchForm`.

## Steps

1. **emits:** grep `$emit(` across `src`. For each component, add an `emits: [...]`
   (or object) declaration listing its custom events. Vue 3 warns on
   undeclared emits and they otherwise fall through to attrs.
2. **v-model on components:** Vue 3 changes the default model prop/event from
   `value`/`input` to `modelValue`/`update:modelValue`.
   - For each component used with `v-model` (e.g. `SearchForm`), update its
     internal `value` prop + `input` emit to `modelValue` +
     `update:modelValue`, **or** keep `value`/`input` and update the call site —
     pick one consistent approach (record it).
   - AF4 has **no `.sync`** (confirmed W0), so no `.sync` → `v-model:arg`
     rewrites are needed.
3. **native v-model** on `<input>`/`<select>` is unchanged — leave it.
4. Re-verify each touched component's event/binding behaviour vs baseline.

## Tests

```sh
cd openy_af4_vue_app && npm run lint && npm run build
grep -rn "\$emit(" src   # cross-check every event is declared
```

Harness: exercise `WizardBar` start-over / view-results, `SearchForm` typing →
`searchKeywords` update, and any other `v-model` component.

## Validation

Owner approves. Every custom emit declared; `v-model` components behave
identically; no Vue 3 emit/attr-fallthrough warnings.

## Out of scope

- Slots (P3).

## Result

(to be filled when phase ships)

`emits` declared across components; `v-model` model-prop strategy applied +
recorded in W5 `DECISIONS.md`; behaviour parity verified.
