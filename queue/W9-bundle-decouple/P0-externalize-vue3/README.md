# P0 — Externalize Vue 3 core (`vue` + `vue-router`)

## Goal

Remove the Vue 3 runtime from `dist/activity_finder_4.umd.min.js` and load it
from `openy_system/vue3` instead, per the `openy_repeat` MR!14 precedent.
Preserve the UMD-consumer contract (global name, mount id) — only the
*internal* bundling of the runtime changes.

## Files

- `openy_af4_vue_app/vite.config.js` — add `build.rollupOptions.external`
  for `vue` / `vue-router`, with a `globals` map to the external's exposed
  global (confirm the exact global name `openy_system/vue3` exposes — check
  `openy_repeat`'s library definition for the pattern).
- `openy_activity_finder.libraries.yml` — add `openy_system/vue3` (and, if
  `vue-router` is externalized separately, whatever library exposes it) as a
  dependency of `activity_finder_4`.
- `openy_af4_vue_app/package.json` — `vue` / `vue-router` move from
  `dependencies` to `devDependencies` (still needed for local dev/build,
  no longer shipped in the bundle).
- Reference: `openy_repeat.libraries.yml` (MR!14) for the exact
  `openy_system/vue3` dependency declaration shape.

## Steps

1. Confirm `openy_system/vue3` is already registered at the platform level
   (from the `openy_repeat` work) or needs adding here — check
   `openy_system` module's `libraries.yml` for an existing `vue3` entry
   before creating a duplicate.
2. Confirm the exact pinned version (`openy_repeat` MR!14 uses Vue 3.5.41 via
   cdnjs) matches or satisfies AF4's `W1-decisions/DECISIONS.md` D3 lock
   (`vue: ^3.4`) — reconcile if the pin is narrower/wider.
3. Add `external`/`globals` to `vite.config.js` for `vue` (and `vue-router`
   if still in use — confirm current AF4 router usage per
   `MIGRATION-REFERENCE.md` §2; `openy_repeat` dropped vue-router entirely in
   favor of `URLSearchParams`/`history.pushState`, AF4 may or may not have
   the same option depending on whether its router does real routing).
4. Rebuild (`npm run build`), measure `dist/activity_finder_4.umd.min.js`
   size before/after — record the delta.
5. Add `openy_system/vue3` as an `activity_finder_4` library dependency in
   `openy_activity_finder.libraries.yml`.
6. Dev-smoke AF4 standalone (mount, wizard flow, results, modals).
7. Cross-app smoke: a page/view with AF3 (or Camp Finder) **and** AF4 both
   present — check browser console for duplicate-Vue warnings, global
   clobber, or double-mount errors.
8. Re-run the W6 baseline screenshot diff (desktop/tablet/mobile) — no
   visual regression expected, this is a delivery-mechanism change only.

## Tests

```sh
cd openy_af4_vue_app
npm run build
ls -la dist/activity_finder_4.umd.min.js   # compare size to pre-W9 baseline
npm run lint
```

Manual: mount AF4 on a page that also has AF3 or Camp Finder active; watch
devtools console for Vue-instance conflicts.

## Validation

- Bundle size reduction recorded in `## Result` below (numbers, not
  estimate).
- No console errors on a mixed AF3+AF4 (or CF+AF4) page.
- W6 baseline screenshots match (re-diff, don't just assume parity).
- `openy_activity_finder.libraries.yml` diff reviewed — confirms
  `activity_finder_4` still resolves the same global/mount id, only the
  dependency list changed.

## Out of scope

- Changing `openy_system/vue3` itself.
- Touching AF3 / Camp Finder Vue 2 code.
- Re-litigating D1–D3 (build tool, BootstrapVue replacement, ecosystem
  version lock).

## Result

- `vite.config.js`: `vue` marked `external` (`output.globals: { vue: 'Vue' }`),
  removed the `vue/dist/vue.esm-bundler.js` local-resolution alias (no longer
  needed — UMD wrapper now reads `require('vue')` / `window.Vue` directly).
  `openy_system/vue3` loads the **global** build
  (`vue.global.prod.min.js`), which is the full runtime+compiler build, so
  the root component's DOM-template mount still compiles at runtime.
- `package.json`: `vue` moved `dependencies` → `devDependencies` (still
  needed locally for build/lint, no longer shipped in the bundle).
  `vue-router` left untouched — grepped `src/`, it is **not imported
  anywhere** in AF4 (dead dependency predating this queue, unrelated to W9;
  flagged in `FOLLOWUPS.md`, not fixed here — out of scope).
- `openy_activity_finder.libraries.yml`: added `openy_system/vue3` to
  `activity_finder_4` dependencies. `openy_system/vue3` already exists
  (`open-y-subprojects/openy_custom`, pinned `3.5.41` via cdnjs), added
  alongside the `openy_repeat` MR!14 work — no duplicate library created.
- **Bundle size:** `dist/activity_finder_4.umd.min.js` **373.4K → 222.3K**
  (**−151.1K, ≈40%**). `.css` unchanged (48.5K, styles aren't affected by
  the JS externalize).
- **Verified:** UMD header now reads
  `require("vue")` / `t.Vue` — inspected the built output directly, confirms
  Rollup did not inline Vue's source (no `function createApp` definition in
  the bundle, only call-sites). `npm run build` and `npm run lint` run
  clean-ish — **`npm run lint` fails on a pre-existing issue** unrelated to
  this change: `.eslintrc.js` requires `babel-eslint`, which isn't in
  `devDependencies` (stale from the eslint-plugin-vue 7 config; W5-P5 is
  supposed to bump this and hasn't shipped). Not introduced by W9-P0, not
  fixed here — flagged in `FOLLOWUPS.md`.
- **Smoke test:** local harness (external `vue@3.5.41` from cdnjs + built
  UMD + stubbed `window.Drupal.t`/`formatPlural`, no real backend) loads
  with **zero console errors** — confirms the external-Vue wiring itself
  doesn't crash boot. The app did **not** visibly mount in that harness,
  because it depends on a live `af/get-data` / session-data endpoint the
  harness doesn't provide — same as pre-W9 behavior with no backend, not a
  regression from externalizing. **Full functional/visual verification
  (including the AF3/Camp-Finder co-load collision check from the wave
  README) requires a live or Mock-backend Drupal site and is still
  outstanding** before this ships — do not merge past P0 without it.

**Status:** code change + build-level verification done. Live-site
functional/visual QA (W6-style) and the cross-app `window.Vue` collision
check remain **pending** — this phase is not done-when-complete until those
run.
