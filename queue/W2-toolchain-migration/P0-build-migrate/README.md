# P0 — Migrate build tool (keep Vue 2)

## Goal

Replace `@vue/cli-service ^4` with the W1-P0 choice (vue-cli 5 or Vite),
keeping AF4 on Vue 2.6, and produce a `dist/` that satisfies the W0-P0 Drupal
contract.

## Files

- `openy_af4_vue_app/package.json` — `devDependencies`, `scripts`.
- `openy_af4_vue_app/vue.config.js` → migrate to vue-cli 5 config **or** a new
  `vite.config.js`.
- `openy_af4_vue_app/babel.config.js`, `postcss.config.js` — adjust/retain.
- `openy_af4_vue_app/.browserslistrc` — retain targets.
- Global SCSS injection (`src/scss/_variables.scss` via
  `style-resources-loader`) — reproduce under the new tool.
- **Do not** touch `src/*.vue` / `src/main.js` Vue logic in this phase.

## Steps

1. Install the chosen build tool + plugins at the W1-P2 locked versions.
2. Port the build config: UMD lib target, single global `activity_finder_4`,
   CSS extracted to `dist/activity_finder_4.css`, global SCSS variables
   injected into every component.
3. **Carry over the externals map** exactly (`vue-router`, `axios`,
   `bootstrap-vue`) so the Vue 2 dist contract is unchanged here. **Record**
   that library mode **auto-externalizes `vue`** — this is the hook W3 (or
   W1-D4 "bundle") flips to bundle Vue 3. Do not change externalization in this
   phase (still Vue 2); just make the override point explicit in config + the
   `## Result`.
4. Keep the `build` / `dev` / `lint` script names (`openy_af4_vue_app.md`
   documents them).
5. `npm run build`. Confirm the two `dist/` artifacts are produced with the
   same filenames and the UMD exposes the same global.
6. Diff the new bundle against the committed `dist/` at the **contract** level
   (global name, CSS classes present, mount behaviour) — not byte-equality
   (minifier output will differ). Note material differences.

## Tests

```sh
cd openy_af4_vue_app
npm ci
npm run lint     # must still pass on Vue 2 source
npm run build    # produces dist/activity_finder_4.umd.min.js + .css
```

Then load the built bundle in the W0-P1 harness (or a Drupal site) and smoke
the wizard — behaviour must be unchanged (still Vue 2).

## Validation

Owner approves. Verify: build is green, `dist/` filenames + UMD global
unchanged, global SCSS variables still applied, AF4 still renders identically
to the W0 baseline (it's the same Vue 2 code).

## Out of scope

- Vue 3 (W3). The framework version does not change here.
- Committing a hand-edited `dist/` — the `dist/` here is a real build output.

## Result

DONE 2026-06-09.

- **Build tool:** `@vue/cli-service ^4` → `vite ^5` + `@vitejs/plugin-vue2 ^2.3.1`
- **Vue:** bumped `^2.6.14` → `^2.7.16` (minor, backward compatible, required by plugin)
- **Removed:** `babel.config.js`, `vue.config.js`, `style-resources-loader`, `vue-cli-plugin-style-resources-loader`, `vue-template-compiler`
- **Added:** `vite.config.js` (UMD lib mode, same output contract)
- **dist/ contract preserved:** `activity_finder_4.umd.min.js` (200KB) + `activity_finder_4.css` (53KB), UMD global `activity_finder_4` ✅
- **global.scss:** `@import "../../node_modules/bootstrap/..."` → `@import "bootstrap/..."` (Vite resolves node_modules directly)
- **Externals carried over unchanged:** `vue→Vue`, `vue-router→VueRouter`, `axios→axios`, `bootstrap-vue→BootstrapVue`
- **W3 flip point:** vite.config.js comment marks where to remove `vue`/`vue-router` from externals to bundle Vue 3 (D4)
- **SCSS deprecation warnings:** Dart Sass `@import` deprecated in favour of `@use` — non-blocking for W2; address in W5 or W7 cleanup
- **Surprises for W3:** resolve.extensions must include `.vue` (Vite does not auto-resolve without it)
