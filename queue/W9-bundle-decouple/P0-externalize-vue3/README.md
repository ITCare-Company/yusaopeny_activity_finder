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

_(pending — fill after ship: exact size before/after, commit SHA)_
