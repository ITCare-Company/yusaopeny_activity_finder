# Wave W3 — Vue 3 core swap

Swap the framework core: `vue` 2 → 3, `vue-router` 3 → 4, `createApp`
bootstrap, compiler, and the global API. By the end of this wave the app
**boots** under Vue 3 (it may still have broken widgets — BootstrapVue is W4,
filters are W5).

**Depends on W2** (build green). **Blocks W4–W5.**

## Phases

| Phase | Goal | Status |
|---|---|---|
| P0 | Deps + `@vue/compiler-sfc` + `createApp` bootstrap. | pending |
| P1 | `vue-router` 3 → 4 (or drop if vestigial). | pending |
| P2 | Global API: `Vue.use/component/config/mixin` → `app.*`. | pending |
| P3 | Boot smoke — app mounts, no white screen. | pending |

## Done when

`npm run build` is green on Vue 3 and the app mounts on `#activity-finder`
without console errors at boot (broken sub-widgets are expected and tracked
for W4/W5).

## Decisions

See [`DECISIONS.md`](DECISIONS.md).

## Out of scope

- BootstrapVue replacement (W4).
- Filters / mixin / emits rewrites (W5) — except the minimum needed to boot.

## Onboarding (UA — Lera)

W3 — серце міграції: ставимо Vue 3, переписуємо запуск застосунку на
`createApp`, оновлюємо роутер. Мета — щоб застосунок просто запустився під
Vue 3 (модалки/фільтри ще можуть бути зламані — це W4/W5).
