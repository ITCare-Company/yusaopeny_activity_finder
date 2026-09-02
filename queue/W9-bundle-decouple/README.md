# Wave W9 — Bundle decouple (externalize Vue 3 core)

Post-ship optimization: pull `vue` + `vue-router` back **out** of the AF4 UMD
bundle and load them from the shared `openy_system/vue3` library instead —
mirroring the pattern shipped in
[`openy_repeat` MR!14](https://git.drupalcode.org/project/openy_repeat/-/merge_requests/14)
(Vue 2 → Vue 3 migration there also externalized the runtime, cutting
`repeat.js` from ~242KB to ~44KB).

**Runs after W7.** AF4's own Vue 2 → Vue 3 migration (W1–W7) intentionally
**bundled** Vue 3 inside the UMD — see `W1-decisions/DECISIONS.md` **D4**.
That decision was made because, at the time, no shared `openy_system/vue3`
library existed and AF3 / Camp Finder still ran Vue 2 off the global
`window.Vue` — externalizing risked a runtime collision. `openy_repeat`
MR!14 has since proven the externalize path (a separate `openy_system/vue3`
library, not reusing the `window.Vue` global AF3/CF depend on), which is the
blocker D4 cited. W9 revisits D4 now that the blocker is resolved.

**Blocks nothing** — this is an optimization wave, not on the critical path
to shipping the Vue 3 migration itself. It can land any time after W7,
independently.

## Phases

| Phase | Goal | Status |
|---|---|---|
| P0 | Externalize `vue` + `vue-router` to `openy_system/vue3`, measure bundle delta, verify no AF3/CF runtime collision. | pending |

## Done when

- `dist/activity_finder_4.umd.min.js` no longer contains the Vue 3 runtime.
- `openy_activity_finder.libraries.yml` declares `activity_finder_4` as
  depending on `openy_system/vue3` (new library, added if not already present
  from the `openy_repeat` work — confirm before duplicating).
- Bundle size delta measured and recorded (before/after, like the
  `openy_repeat` 242KB→44KB figure).
- W6 baseline screenshots re-verified — no visual/functional regression.
- A page that loads AF3 (or Camp Finder, Vue 2) **and** AF4 (Vue 3,
  externalized) together shows no console error / duplicate-mount / global
  clobber.

## Out of scope

- Any change to AF3 (`openy_af_vue_app/`) or Camp Finder (`openy_cf_vue_app/`)
  — still Vue 2, still `window.Vue`, untouched.
- Any change to the `openy_system/vue3` library itself (consume only).
- Re-opening D1–D3 (build tool, BootstrapVue replacement, ecosystem
  versions) — those stay locked.

## Onboarding (UA — Lera)

W9 — це вже після того, як AF4 на Vue 3 засайплено (W7). Ідея: замість того,
щоб Vue 3 їхав **всередині** зібраного файлу AF4 (як вирішили в D4 через
відсутність спільної бібліотеки), тепер винести його в спільну
`openy_system/vue3` — так само, як щойно зробили в `openy_repeat` (MR!14).
Це зменшує розмір файлу, який вантажить браузер. Головне — перевірити, що
AF3 і Camp Finder (вони на Vue 2, через `window.Vue`) не конфліктують з
винесеним Vue 3.
