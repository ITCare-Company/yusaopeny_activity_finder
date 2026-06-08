# Queue — yusaopeny_activity_finder (AF4 Vue 2 → Vue 3 upgrade)

Hierarchical work plan for **ITCR-1273** — upgrade the Activity Finder v4
(`openy_af4_vue_app/`) Vue application from **Vue 2.6** to **Vue 3**.

Pattern adapted from
[`ymca_ws_canvas_demo/queue`](https://github.com/ITCare-Company/ymca_ws_canvas_demo/tree/feat/standard-pass-w1/queue)
and ultimately
[`gogs-cli-queue`](https://github.com/ITCare-Company/template_for_agents/tree/main/process-knowledge-base/gogs-cli-queue).

Each wave = directory. Each phase = subdirectory with a `README.md`
containing the plan (`Goal / Files / Steps / Tests / Validation / Out of
scope / Result`). Each wave gates the next.

> **How this queue started:** see [`START-PROMPT.md`](START-PROMPT.md) — the
> kickoff request, verbatim, plus onboarding for Lera.

---

## What we are migrating (read this first)

`openy_activity_finder` ships **three** Vue apps. This queue touches **only
AF4**:

| App | Folder | In scope? |
|---|---|---|
| Activity Finder v3 | `openy_af_vue_app/` | **No** — out of scope (uses global EventBus; separate effort) |
| **Activity Finder v4** | **`openy_af4_vue_app/`** | **Yes — this queue** |
| Camp Finder | `openy_cf_vue_app/` | **No** — out of scope (separate effort) |

AF4 builds to a **UMD library** (`dist/activity_finder_4.umd.min.js` +
`.css`) consumed by Drupal via `openy_activity_finder.libraries.yml`
(`activity_finder_4` library) and mounted on the `#activity-finder` element.
**That external contract must survive the migration byte-for-byte at the
consumer boundary** — Drupal must keep loading the same library, same global,
same mount point.

## Why Vue 3

Vue 2 reached EOL on **2023-12-31**. No security patches, ecosystem drift
(BootstrapVue 2, vue-cli 4, `vue-template-compiler` all frozen). ITCR-1273
requests the bump because AF is widely deployed across YMCA Open Y sites.

## The migration surface (measured, not guessed)

Audited on the `6.x` branch of the fork — exact counts feed the phases:

| Surface | Where | Vue 3 impact |
|---|---|---|
| Global app bootstrap | `src/main.js` — `new Vue({...}).$mount('#activity-finder')` | `createApp(App).use(router).mount(...)` |
| Global plugins/config | `src/main.js` — `Vue.use`, `Vue.component`, `Vue.config`, `Vue.mixin` | move to `app.use` / `app.component` / `app.config.globalProperties` / `app.mixin` |
| **Template filters** | `Vue.filter('capitalize'|'t'|'formatPlural')` + **~103** `\| filter` usages in templates | **filters removed in Vue 3** — convert to methods/computed/global props |
| Router | `src/router/index.js` — `new VueRouter({mode:'history'})`, routes empty | `createRouter({history: createWebHistory()})` |
| **BootstrapVue** | 7 files: `Modal`, `Fieldset`, `Foldable`, `FoldableInput`, `filters/Ages`, `ResultsBar`, `steps/SelectAges` + `main.js` | **no Vue 3 build of `bootstrap-vue`** — replacement required (W1 decision) |
| FontAwesome | `@fortawesome/vue-fontawesome ^2` | → `^3` (Vue 3 build) |
| Iconify | `@iconify/vue2` | → `@iconify/vue` |
| Build tool | `@vue/cli-service ^4`, `vue-template-compiler ^2` | → vue-cli 5 or Vite + `@vue/compiler-sfc` (W1 decision) |
| Slots | `v-slot` / scoped slots in **17** components | syntax mostly compatible; audit for `slot=`/`slot-scope=` legacy |
| Components | **44** `.vue` files | per-component emits/v-model review |

**Not present in AF4** (confirms smaller scope than AF3/CF): no global
`EventBus`, no `$on`/`$off`, no `.sync`, no `$listeners`/`$children`/`$attrs`.

---

## Active waves

| Wave | Summary | Spec |
|---|---|---|
| **W0-baseline-contract** | Freeze the "before": Drupal integration contract, behavioral baseline (golden screenshots → Ira's parity checklist), exact breaking-change surface audit. Gates everything. | [`W0-baseline-contract/`](W0-baseline-contract/) |
| W1-decisions | Lock the three open choices: build tool (vue-cli 5 vs Vite), BootstrapVue replacement, Vue-3 ecosystem versions. Output = `DECISIONS.md`. | [`W1-decisions/`](W1-decisions/) |
| W2-toolchain-migration | Migrate the build tool **while still on Vue 2**. De-risk toolchain before the core swap. Identical dist contract. | [`W2-toolchain-migration/`](W2-toolchain-migration/) |
| W3-vue3-core-swap | `vue` 2→3, `vue-router` 3→4, `createApp`, compiler swap, global API. App boots. | [`W3-vue3-core-swap/`](W3-vue3-core-swap/) |
| W4-bootstrap-vue-migration | Replace BootstrapVue per-file (the 7 consumers). One phase per file/cluster. | [`W4-bootstrap-vue-migration/`](W4-bootstrap-vue-migration/) |
| W5-vue3-api-rewrites | Filters removal (~103 pipes), global mixin → global props/composable, emits/v-model audit, slots sweep, FontAwesome/Iconify bump, eslint-plugin-vue 9. | [`W5-vue3-api-rewrites/`](W5-vue3-api-rewrites/) |
| W6-qa-visual-functional | Walk every screen/step/modal/filter at desktop/tablet/mobile, diff vs W0 baseline. **Ira's wave** — driven by the progress table in `INDEX.md`. | [`W6-qa-visual-functional/`](W6-qa-visual-functional/) |
| W7-drupal-integration-ship | Production build, verify UMD global + `.css` + `libraries.yml` contract, drush smoke on a real Open Y site, open PR. | [`W7-drupal-integration-ship/`](W7-drupal-integration-ship/) |
| W8-retro-upstream | **After all done.** Harvest lessons, PR them into the shared migration anatomy, subtree-import this queue as a PKB entry, back-reference + close ITCR-1273. Fulfils the anatomy-upstream-PR obligation. | [`W8-retro-upstream/`](W8-retro-upstream/) |

**Gating.** W0 blocks all (no migration without a frozen baseline). W1 blocks
W2–W5 (toolchain/dep choices). W2 blocks W3 (build green before core swap).
W3 blocks W4–W5. W4+W5 block W6 (nothing to QA until code migrated). W6
blocks W7 (do not ship un-QA'd output). W7 blocks W8 (only retro a shipped
migration). W8 returns lessons to
[`template_for_agents` migration anatomy](https://github.com/ITCare-Company/template_for_agents/blob/main/process-knowledge-base/MIGRATION-QUEUE-ANATOMY.md).

## Conventions

- Plan format per phase: `Goal / Files / Steps / Tests / Validation / Out of
  scope`. Append `## Result` when shipped.
- **Source of truth is the Vue source under `openy_af4_vue_app/src/`.** The
  committed `dist/*.umd.min.js` is a build artifact — never hand-edit it;
  regenerate via `npm run build` (per `openy_af4_vue_app.md`).
- One PR per wave (or one PR for the whole epic if the owner prefers a single
  review surface). Stack follow-up commits on the same branch — do not open a
  second PR for the same wave (per `feedback_one_pr_per_repo`).
- See [`RULES.md`](RULES.md) for commit shape, pre-flight gates, approval gates.

## House rules

- **Preserve the Drupal consumer contract.** UMD global name
  `activity_finder_4`, mount id `#activity-finder`, `dist/` artifact paths,
  and `openy_activity_finder.libraries.yml` entries must not change without an
  explicit decision in the wave `DECISIONS.md`. (W0-P0, W7-P0.)
- **No fabricated behaviour** (per `feedback_no_fabricated_behavior`). Every
  rewrite is a faithful port. No invented props, defaults, routes, or i18n
  keys. `window.Drupal.t` / `formatPlural` semantics stay identical.
- **No `dist/` edits.** Only `src/` changes; `dist/` is regenerated and
  committed by the build phase (W7).
- **Approval gate per phase** (per `feedback_approval_gate_all_fixes`). No
  mechanical batch fixes — each phase gets Plan → Verify → push approval.
- **Atomic commits, no branch hopping** (per
  `feedback_atomic_commits_no_branch_hop`). One phase = one commit on the wave
  branch.
- All team-facing onboarding / Q&A in **simple Ukrainian** (per
  `feedback_queue_simple_ukrainian`); technical specs stay in English.

## Pinned references

| anchor | role |
|---|---|
| [ITCR-1273](https://jet-dev.atlassian.net/browse/ITCR-1273) | the upgrade ticket (Lera, owner) |
| [yusaopeny_activity_finder (fork)](https://github.com/ITCare-Company/yusaopeny_activity_finder) | working fork — this queue lives here |
| [Vue 3 Migration Guide](https://v3-migration.vuejs.org/) | canonical breaking-change list |
| [vue-router v4 migration](https://router.vuejs.org/guide/migration/) | router 3→4 |
| [`ymca_ws_canvas_demo/queue`](https://github.com/ITCare-Company/ymca_ws_canvas_demo/tree/feat/standard-pass-w1/queue) | queue anatomy this one is adapted from |
| `STANDARD-PASS-PROGRESS.md` (Lera) | source of the progress-table extension folded into [`INDEX.md`](INDEX.md) + [`inventory.tsv`](inventory.tsv) |

## Onboarding (UA — для Lera і команди)

Коротко що це і навіщо.

- **AF4** = застосунок Activity Finder версії 4, у папці `openy_af4_vue_app/`.
  Написаний на **Vue 2**. Збирається в один UMD-файл, який Drupal підключає
  через `openy_activity_finder.libraries.yml` і монтує на `#activity-finder`.
- **Завдання (ITCR-1273)** — підняти його з **Vue 2 на Vue 3**, бо Vue 2 більше
  не підтримується.
- Головна складність — **BootstrapVue** (модалки, поля) не має версії під
  Vue 3, тому в W1 ми вирішуємо, чим його замінити. І **фільтри** (`| t`,
  `| capitalize`, `| formatPlural`) у Vue 3 видалені — їх близько сотні в
  шаблонах, треба переписати на методи.

Порядок хвиль простими словами:

- **W0** — зафіксувати «як було»: знімки екранів кожного кроку (це чек-лист
  для Іри), і точний список того, що ламається.
- **W1** — ухвалити рішення (чим збирати, чим замінити BootstrapVue, які
  версії бібліотек).
- **W2** — спочатку поміняти лише систему збірки, ще на Vue 2 (щоб менше
  ризику).
- **W3** — власне перехід ядра на Vue 3.
- **W4** — замінити BootstrapVue по одному файлу.
- **W5** — переписати фільтри, mixin, emits під Vue 3.
- **W6** — Іра проходить оком кожен екран на desktop / tablet / mobile і
  порівнює з W0. Таблиця прогресу — в [`INDEX.md`](INDEX.md).
- **W7** — фінальна збірка, перевірка на живому сайті, PR.

Кожна фаза має `README.md` із розділами `Goal / Files / Steps / Tests /
Validation / Out of scope`. Перед кодом — `Plan` у чат, чекай «вйо». Після —
`Verify` (скрін / лог), чекай «ok / push».
