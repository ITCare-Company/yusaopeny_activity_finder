# P5 — eslint-plugin-vue 9 + prettier bump

## Goal

Upgrade the lint stack to Vue 3 rules and make `npm run lint` pass clean —
the wave's exit gate.

## Files

- `openy_af4_vue_app/.eslintrc.js`
- `openy_af4_vue_app/package.json` — `eslint-plugin-vue ^9`,
  `@vue/eslint-config-prettier`, `prettier`, `eslint` (W1-P2 versions);
  swap `babel-eslint` → `@babel/eslint-parser` if still referenced.
- Source files with lint violations surfaced by the new rules.

## Steps

1. Bump the lint deps to the locked versions; update `.eslintrc.js` to the
   Vue 3 recommended config (`plugin:vue/vue3-recommended` or the agreed
   ruleset).
2. Run `npm run lint`; triage violations:
   - auto-fixable (`--fix`) formatting — apply.
   - rule changes needing manual edits — fix faithfully (no behaviour change).
3. Keep the existing prettier options (`printWidth: 100`, `semi: false`,
   `singleQuote: true`, etc. from `package.json`) unless the owner agrees to
   change them.
4. Confirm lint + build both green.

## Tests

```sh
cd openy_af4_vue_app && npm run lint && npm run build
```

## Validation

Owner approves. `npm run lint` passes under eslint-plugin-vue 9; no behaviour
change from `--fix`; prettier style preserved.

## Out of scope

- Functional rewrites (P0–P4).

## Result

(to be filled when phase ships)

Lint stack on Vue 3 rules; `npm run lint` green; W5 complete → W6 may start.
