# P0 — Build-tool decision

## Goal

Decide whether AF4 stays on `@vue/cli-service` (upgraded 4 → 5) or moves to
Vite, given the hard constraint: the output must remain a **UMD library**
named `activity_finder_4` producing `dist/activity_finder_4.umd.min.js` +
`.css`, consumable by Drupal exactly as today.

## Files

Read-only research. Output: a recommendation block appended to
[`../DECISIONS.md`](../DECISIONS.md).

Sources:
- `openy_af4_vue_app/package.json` (current `build` script: `vue-cli-service build --target lib --formats umd-min --name activity_finder_4 src/main.js`).
- `openy_af4_vue_app/vue.config.js`, `babel.config.js`, `postcss.config.js`.
- The other two apps' build setups (`openy_af_vue_app`, `openy_cf_vue_app`) — consistency consideration.

## Steps

1. List what the current build does that must be preserved: UMD format, single
   global name, CSS extracted to a sibling file, `style-resources-loader` for
   global SCSS (`src/scss/_variables.scss`), browserslist targets.
2. Evaluate **vue-cli 5**: smallest migration (config shape familiar), still
   supports `--target lib --formats umd-min`. Risk: vue-cli is in maintenance.
3. Evaluate **Vite (library mode)**: modern, faster, but UMD lib output +
   global SCSS injection + single-file CSS need explicit `build.lib` +
   `rollupOptions` config; verify a UMD single-global build is reproducible.
4. Compare on: effort, UMD-contract fidelity, SCSS global-inject parity,
   long-term support, consistency with AF3/CF if they migrate later.
5. Recommend one, with the exact build command/config sketch.

## Tests

No code. A throwaway proof-of-concept build (still Vue 2) may be run to
confirm UMD output is reproducible — but the actual build migration is W2.

## Validation

Owner approves the choice. The decision names the tool, the exact build
command, and how the UMD global + single-CSS-file contract is preserved.

## Out of scope

- Performing the migration (W2).
- Vue 3 itself (W3).

## Result

DONE 2026-06-09. Decision recorded in W1/DECISIONS.md.

Build tool chosen and recorded in W1 `DECISIONS.md` with the build-command
sketch and UMD-contract preservation note.
