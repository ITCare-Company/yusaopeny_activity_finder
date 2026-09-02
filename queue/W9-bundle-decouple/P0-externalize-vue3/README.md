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
- **Live-site verification (westcookymca.org, remote ddev, Mock backend):**
  a throwaway local harness with a stubbed backend was **not sufficient** —
  it never caught that `openy_system/vue3` didn't actually exist in the
  site's locked dependency version. Redid the check against a real,
  running Open Y site instead:
  - Path-repo'd this branch into `westcookymca.org` (`ddev composer update`),
    placed the `activity_finder_4` block on the front page (Mock backend —
    this site has no Solr).
  - **First real bug caught:** `open-y-subprojects/openy_custom` was
    locked to `3.1.5` on that site — `vue3:` was only added in `3.2.0`
    (commit `35ae6b68`, same ITCR-1273/ITCR-1301 work). Without a version
    constraint, `openy_system/vue3` doesn't resolve and Drupal throws on
    the library definition. **Fixed properly**: added
    `"open-y-subprojects/openy_custom": "^3.2.0"` to this module's own
    `composer.json` `require` (separate commit) — not a one-off `composer
    require` on the test site, an actual dependency declaration so this
    doesn't silently break wherever AF4 installs next.
  - **After the fix:** loaded the real page. `#activity-finder` mounted,
    full wizard rendered (Age/Day & Time/Location/Activity + keyword
    search), `openy_system/vue3` (cdnjs 3.5.41 global build) loaded and
    is what's driving it. Clicked into "Activity" — step routing via
    `?step=selectActivities` worked, category list rendered from the Mock
    backend (Health and Fitness, Kids and Family Activities, Swimming).
  - **Second real bug caught, unrelated to W9:** clicking into a step
    throws `TypeError: Cannot read properties of undefined (reading
    'getBoundingClientRect')` in `Step.vue`'s `handleSticky()` —
    `this.$refs.bottom` is referenced but no element in the template
    carries `ref="bottom"` (only `bottomDesktop` exists). Non-fatal (page
    stays interactive), but a genuine pre-existing gap in the Vue 3
    migration — also uses the Vue 2 lifecycle name `beforeDestroy` instead
    of `beforeUnmount`. **Not fixed here** — out of scope for W9-P0 (it's
    a W4/W5 migration bug, would reproduce identically with Vue bundled),
    flagged in `FOLLOWUPS.md` for Lera.
  - **AF3/Camp-Finder `window.Vue` collision — attempted, inconclusive.**
    Placed the AF3 block (`activity_finder_block`) on the same page to
    check the collision this phase exists to de-risk. It never got that
    far: AF3's block config on this site points at
    `openy_activity_finder.solr_backend`, which isn't registered (site
    only has `mock`) — **500 error before any JS ran**, unrelated to Vue
    or this change. Removed the test block. **The actual collision check
    is still unverified** — needs a site where AF3 (or Camp Finder) has a
    working backend configured, or a synthetic page that mounts both
    without going through the block config path.
  - Cleaned up: both test blocks deleted, test site's `composer.json` /
    `composer.lock` reverted to their pre-test state (only this repo's
    branch carries the real fix).

**Status:** Externalize wiring **verified working on a real, running Open Y
site** (not a synthetic harness) — bundle loads, mounts, renders, and is
interactive against a Mock backend, zero console errors from the externalize
change itself. One dependency-version bug found and fixed as part of this
phase (`openy_custom` ^3.2.0). One pre-existing, unrelated Vue3-migration bug
found and flagged, not fixed. **Still pending before ship:** the AF3/Camp
Finder `window.Vue` collision check (blocked on finding/building an
environment where AF3 actually boots), and a full W6-style visual diff
against the W0 baseline.
