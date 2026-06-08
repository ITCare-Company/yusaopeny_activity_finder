# Wave W4 — BootstrapVue migration

Replace BootstrapVue (no Vue 3 build) across the 7 AF4 consumers using the
W1-P1 strategy, **one file/cluster per phase**, then remove the global plugin
and prune the dependency. Like-for-like — preserve markup/classes so the W0
baseline still matches.

**Depends on W1-P1 (strategy) + W3 (app boots). Blocks W6.**

## Phases

| Phase | File(s) | Status |
|---|---|---|
| P0 | `modals/Modal.vue` — core shell, every modal depends on it | pending |
| P1 | `Fieldset.vue` | pending |
| P2 | `Foldable.vue` + `FoldableInput.vue` | pending |
| P3 | `filters/Ages.vue` + `steps/SelectAges.vue` | pending |
| P4 | `ResultsBar.vue` | pending |
| P5 | remove `Vue.use(BootstrapVue)` shim, prune dep, dead-CSS reconcile | pending |

P0 first (modal shell is a dependency of the modal screens). P1–P4 parallel in
principle but each is its own approval-gated phase. P5 last (only after no
consumer references BootstrapVue).

## Done when

No `bootstrap-vue` import remains in `src/`; the dependency is removed from
`package.json`; every replaced widget renders identical markup to the W0
baseline.

## Decisions

See [`DECISIONS.md`](DECISIONS.md).

## Out of scope

- Restyling — like-for-like only.
- Filters / mixin / emits (W5).

## Onboarding (UA — Lera)

W4 — по черзі замінюємо BootstrapVue у 7 файлах (спочатку базова модалка, бо
від неї залежать інші). Замінюємо так, щоб верстка лишилась такою ж, як у W0.
Наприкінці прибираємо саму бібліотеку.
