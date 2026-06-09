# P2 — Vue-3 ecosystem version lock

## Goal

Pin the exact target versions for every dependency the migration touches, so
W2–W5 install against a fixed set and `package.json` changes are reviewable.

## Files

Read-only research. Output: a version table in
[`../DECISIONS.md`](../DECISIONS.md).

Source: `openy_af4_vue_app/package.json` (current versions in
`MIGRATION-REFERENCE.md` → "Dependency deltas").

## Steps

1. Pin core: `vue ^3.x`, `vue-router ^4.x`, `@vue/compiler-sfc` (matching vue).
2. Pin UI/icons: `@fortawesome/vue-fontawesome ^3`, `@fortawesome/fontawesome-svg-core` / `free-solid-svg-icons` (confirm compatible majors), `@iconify/vue` (replacing `@iconify/vue2`).
3. Pin BootstrapVue replacement dep(s) per W1-P1 (e.g. `bootstrap-vue-next` version, or `bootstrap ^5` if the plain-markup route needs it).
4. Pin tooling: build tool per W1-P0 (`@vue/cli-service ^5` **or** `vite` + `@vitejs/plugin-vue`), `eslint-plugin-vue ^9`, `@vue/eslint-config-prettier` + `prettier` current, `sass` (already `^1.89`).
5. Remove from `devDependencies`: `vue-template-compiler`, `babel-eslint` (→ `@babel/eslint-parser` if still on babel-eslint), and any vue-cli plugins not needed under the chosen build tool.
6. Note peer-dependency constraints and any package that has no clean Vue 3
   equivalent (flag for a follow-up decision).

## Tests

No code. The lock is validated by W2/W3 installs succeeding.

## Validation

Owner approves the version table. Every current dep has a target (kept,
bumped, replaced, or removed) with no "TBD" rows.

## Out of scope

- Installing the versions (W2/W3).

## Result

DONE 2026-06-09. Decision recorded in W1/DECISIONS.md.

Version table recorded in W1 `DECISIONS.md`. `MIGRATION-REFERENCE.md`
"Dependency deltas" reconciled with the locked versions.
