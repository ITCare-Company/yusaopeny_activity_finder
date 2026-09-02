# Index

Flat enumeration of every plan file in the queue. Mark status next to each.
Keep order by wave, then phase.

**Migration Reference (exact breaking-change surface):** [`MIGRATION-REFERENCE.md`](MIGRATION-REFERENCE.md)
**How the queue started (onboarding):** [`START-PROMPT.md`](START-PROMPT.md)

## Reference environments

> Lera's extension — pin the live URLs the reviewer (Ira) compares against.
> Fill the AF4 sandbox URL once stood up in W0-P1 (backend = Mock/DB, no Solr).

| Env | URL | Role |
|---|---|---|
| AF4 sandbox (Vue 2, "before") | _(stand up in W0-P1, backend Mock/DB)_ | primary baseline source for golden screenshots |
| AF4 real site (Vue 2, upgrade) | _(nominate in W0-P1b)_ | upgrade-path baseline for W7 |
| AF4 dev harness | `openy_af4_vue_app` `npm run dev` + local mount | iterate during W3–W5 |
| AF4 migrated (Vue 3, "after") | _(same sandbox, post-W7 build)_ | W6 parity target |
| Vue 3 Migration Guide | https://v3-migration.vuejs.org/ | breaking-change canon |

---

## W0 — Baseline & contract (gates all)

| Phase | Status | Path |
|---|---|---|
| P0 Drupal consumer contract inventory | pending | [`W0-baseline-contract/P0-drupal-contract/README.md`](W0-baseline-contract/P0-drupal-contract/README.md) |
| P1 Behavioral baseline — sandbox golden screenshots | pending | [`W0-baseline-contract/P1-behavioral-baseline/README.md`](W0-baseline-contract/P1-behavioral-baseline/README.md) |
| P1b Real-site upgrade-path baseline | pending | [`W0-baseline-contract/P1b-realsite-upgrade/README.md`](W0-baseline-contract/P1b-realsite-upgrade/README.md) |
| P2 Breaking-change surface audit | pending | [`W0-baseline-contract/P2-breaking-surface-audit/README.md`](W0-baseline-contract/P2-breaking-surface-audit/README.md) |

## W0b — Backend plugin system & local-dev harness (gates W0-P1; unblocks migration)

| Phase | Status | Path |
|---|---|---|
| P0 Backend plugin type + block selector | **shipped** (PR #4) | [`W0b-backend-harness/P0-plugin-manager/README.md`](W0b-backend-harness/P0-plugin-manager/README.md) |
| P1 Extract Solr behind the plugin → submodule | **shipped** (PR #4) | [`W0b-backend-harness/P1-solr-plugin/README.md`](W0b-backend-harness/P1-solr-plugin/README.md) |
| P2 Mock backend (fixtures, no Solr) — default; unblocks migration | **shipped** (PR #4) | [`W0b-backend-harness/P2-mock-plugin/README.md`](W0b-backend-harness/P2-mock-plugin/README.md) |
| P3 DB backend (entity query, no Solr) | not built (deferred; Mock covers it) | [`W0b-backend-harness/P3-db-plugin/README.md`](W0b-backend-harness/P3-db-plugin/README.md) |
| P4 Seed demo content (LB Mock + paragraph Solr) | **shipped** (PR #4) | [`W0b-backend-harness/P4-demo-content/README.md`](W0b-backend-harness/P4-demo-content/README.md) |
| P5 Legacy-config fallback (inherit-global done; full mapping deferred — Lera→Vlad) | partial | [`W0b-backend-harness/P5-legacy-config-fallback/README.md`](W0b-backend-harness/P5-legacy-config-fallback/README.md) |
| P6 Upgrade path (hook_update: legacy service-id → plugin id + enable Solr submodule) | **shipped** (PR #4) | [`W0b-backend-harness/P6-upgrade-path/README.md`](W0b-backend-harness/P6-upgrade-path/README.md) |

## W1 — Decisions (gates W2–W5)

| Phase | Status | Path |
|---|---|---|
| P0 Build-tool decision (vue-cli 5 vs Vite) | pending | [`W1-decisions/P0-build-tool/README.md`](W1-decisions/P0-build-tool/README.md) |
| P1 BootstrapVue replacement decision | pending | [`W1-decisions/P1-bootstrap-vue-replacement/README.md`](W1-decisions/P1-bootstrap-vue-replacement/README.md) |
| P2 Vue-3 ecosystem version lock | pending | [`W1-decisions/P2-ecosystem-version-lock/README.md`](W1-decisions/P2-ecosystem-version-lock/README.md) |

## W2 — Toolchain migration (still Vue 2)

| Phase | Status | Path |
|---|---|---|
| P0 Migrate build tool, keep Vue 2, identical dist | pending | [`W2-toolchain-migration/P0-build-migrate/README.md`](W2-toolchain-migration/P0-build-migrate/README.md) |

## W3 — Vue 3 core swap

| Phase | Status | Path |
|---|---|---|
| P0 deps + compiler + `createApp` | pending | [`W3-vue3-core-swap/P0-deps-compiler-createapp/README.md`](W3-vue3-core-swap/P0-deps-compiler-createapp/README.md) |
| P1 vue-router 3 → 4 | pending | [`W3-vue3-core-swap/P1-router4/README.md`](W3-vue3-core-swap/P1-router4/README.md) |
| P2 global API (`Vue.*` → `app.*`) | pending | [`W3-vue3-core-swap/P2-global-api/README.md`](W3-vue3-core-swap/P2-global-api/README.md) |
| P3 boot smoke (white-screen check) | pending | [`W3-vue3-core-swap/P3-boot-smoke/README.md`](W3-vue3-core-swap/P3-boot-smoke/README.md) |

## W4 — BootstrapVue migration (per file)

| Phase | Status | Path |
|---|---|---|
| P0 `modals/Modal.vue` (core shell) | pending | [`W4-bootstrap-vue-migration/P0-modal/README.md`](W4-bootstrap-vue-migration/P0-modal/README.md) |
| P1 `Fieldset.vue` | pending | [`W4-bootstrap-vue-migration/P1-fieldset/README.md`](W4-bootstrap-vue-migration/P1-fieldset/README.md) |
| P2 `Foldable.vue` + `FoldableInput.vue` | pending | [`W4-bootstrap-vue-migration/P2-foldable/README.md`](W4-bootstrap-vue-migration/P2-foldable/README.md) |
| P3 `filters/Ages.vue` + `steps/SelectAges.vue` | pending | [`W4-bootstrap-vue-migration/P3-ages/README.md`](W4-bootstrap-vue-migration/P3-ages/README.md) |
| P4 `ResultsBar.vue` | pending | [`W4-bootstrap-vue-migration/P4-resultsbar/README.md`](W4-bootstrap-vue-migration/P4-resultsbar/README.md) |
| P5 remove `Vue.use(BootstrapVue)` + prune dep + dead-CSS reconcile | pending | [`W4-bootstrap-vue-migration/P5-remove-global/README.md`](W4-bootstrap-vue-migration/P5-remove-global/README.md) |
| P6 hand-rolled modal a11y restore (focus trap / aria / scroll-lock) | pending | [`W4-bootstrap-vue-migration/P6-modal-a11y/README.md`](W4-bootstrap-vue-migration/P6-modal-a11y/README.md) |

## W5 — Vue 3 API rewrites

| Phase | Status | Path |
|---|---|---|
| P0 filters removal (~103 pipes → methods) | pending | [`W5-vue3-api-rewrites/P0-filters-removal/README.md`](W5-vue3-api-rewrites/P0-filters-removal/README.md) |
| P1 global mixin → global props / composable | pending | [`W5-vue3-api-rewrites/P1-global-mixin/README.md`](W5-vue3-api-rewrites/P1-global-mixin/README.md) |
| P2 emits + v-model audit (44 components) | pending | [`W5-vue3-api-rewrites/P2-emits-vmodel/README.md`](W5-vue3-api-rewrites/P2-emits-vmodel/README.md) |
| P3 slots / legacy syntax sweep | pending | [`W5-vue3-api-rewrites/P3-slots-sweep/README.md`](W5-vue3-api-rewrites/P3-slots-sweep/README.md) |
| P4 FontAwesome v3 + Iconify swap | pending | [`W5-vue3-api-rewrites/P4-fontawesome-iconify/README.md`](W5-vue3-api-rewrites/P4-fontawesome-iconify/README.md) |
| P5 eslint-plugin-vue 9 + prettier bump | pending | [`W5-vue3-api-rewrites/P5-lint-prettier/README.md`](W5-vue3-api-rewrites/P5-lint-prettier/README.md) |
| P6 axios → native fetch | pending | [`W5-vue3-api-rewrites/P6-axios-fetch/README.md`](W5-vue3-api-rewrites/P6-axios-fetch/README.md) |
| P7 fetch error + config parity (follow-up to P6) | pending | [`W5-vue3-api-rewrites/P7-fetch-error-parity/README.md`](W5-vue3-api-rewrites/P7-fetch-error-parity/README.md) |

## W6 — QA visual + functional (Ira)

| Phase | Status | Path |
|---|---|---|
| P0 Entry + SelectPath | pending | [`W6-qa-visual-functional/P0-entry-selectpath/README.md`](W6-qa-visual-functional/P0-entry-selectpath/README.md) |
| P1 Wizard steps | pending | [`W6-qa-visual-functional/P1-wizard-steps/README.md`](W6-qa-visual-functional/P1-wizard-steps/README.md) |
| P2 Results | pending | [`W6-qa-visual-functional/P2-results/README.md`](W6-qa-visual-functional/P2-results/README.md) |
| P3 Modals | pending | [`W6-qa-visual-functional/P3-modals/README.md`](W6-qa-visual-functional/P3-modals/README.md) |
| P4 Filters | pending | [`W6-qa-visual-functional/P4-filters/README.md`](W6-qa-visual-functional/P4-filters/README.md) |
| P5 Responsive (1920 / 1024 / 468) | pending | [`W6-qa-visual-functional/P5-responsive/README.md`](W6-qa-visual-functional/P5-responsive/README.md) |

## W7 — Drupal integration & ship

| Phase | Status | Path |
|---|---|---|
| P0 Production build + contract verify | pending | [`W7-drupal-integration-ship/P0-prod-build-contract/README.md`](W7-drupal-integration-ship/P0-prod-build-contract/README.md) |
| P1 drush smoke on a live Open Y site | pending | [`W7-drupal-integration-ship/P1-drush-smoke/README.md`](W7-drupal-integration-ship/P1-drush-smoke/README.md) |
| P3 CSP review for esm-bundler runtime compiler | pending | [`W7-drupal-integration-ship/P3-csp-runtime-compiler/README.md`](W7-drupal-integration-ship/P3-csp-runtime-compiler/README.md) |
| P2 PR to fork | pending | [`W7-drupal-integration-ship/P2-pr/README.md`](W7-drupal-integration-ship/P2-pr/README.md) |

## W8 — Retro & upstream (after all done)

| Phase | Status | Path |
|---|---|---|
| P0 Lessons-learned harvest | pending | [`W8-retro-upstream/P0-lessons-harvest/README.md`](W8-retro-upstream/P0-lessons-harvest/README.md) |
| P1 Enrich upstream anatomy doc (PR) | pending | [`W8-retro-upstream/P1-upstream-anatomy-pr/README.md`](W8-retro-upstream/P1-upstream-anatomy-pr/README.md) |
| P2 Subtree-import queue as PKB entry | pending | [`W8-retro-upstream/P2-subtree-import-pkb/README.md`](W8-retro-upstream/P2-subtree-import-pkb/README.md) |
| P3 Back-reference + close ITCR-1273 | pending | [`W8-retro-upstream/P3-backreference-close/README.md`](W8-retro-upstream/P3-backreference-close/README.md) |

## W9 — Bundle decouple (post-ship, independent of W0–W8)

| Phase | Status | Path |
|---|---|---|
| P0 Externalize `vue`/`vue-router` to `openy_system/vue3` | pending | [`W9-bundle-decouple/P0-externalize-vue3/README.md`](W9-bundle-decouple/P0-externalize-vue3/README.md) |

## Wave-level docs

| Wave | README | DECISIONS |
|---|---|---|
| W0 | [`W0-baseline-contract/README.md`](W0-baseline-contract/README.md) | [`DECISIONS.md`](W0-baseline-contract/DECISIONS.md) |
| W0b | [`W0b-backend-harness/README.md`](W0b-backend-harness/README.md) | [`DECISIONS.md`](W0b-backend-harness/DECISIONS.md) |
| W1 | [`W1-decisions/README.md`](W1-decisions/README.md) | [`DECISIONS.md`](W1-decisions/DECISIONS.md) |
| W2 | [`W2-toolchain-migration/README.md`](W2-toolchain-migration/README.md) | (single-phase wave) |
| W3 | [`W3-vue3-core-swap/README.md`](W3-vue3-core-swap/README.md) | [`DECISIONS.md`](W3-vue3-core-swap/DECISIONS.md) |
| W4 | [`W4-bootstrap-vue-migration/README.md`](W4-bootstrap-vue-migration/README.md) | [`DECISIONS.md`](W4-bootstrap-vue-migration/DECISIONS.md) |
| W5 | [`W5-vue3-api-rewrites/README.md`](W5-vue3-api-rewrites/README.md) | [`DECISIONS.md`](W5-vue3-api-rewrites/DECISIONS.md) |
| W6 | [`W6-qa-visual-functional/README.md`](W6-qa-visual-functional/README.md) | [`DECISIONS.md`](W6-qa-visual-functional/DECISIONS.md) |
| W7 | [`W7-drupal-integration-ship/README.md`](W7-drupal-integration-ship/README.md) | (single-surface wave) |
| W8 | [`W8-retro-upstream/README.md`](W8-retro-upstream/README.md) | [`DECISIONS.md`](W8-retro-upstream/DECISIONS.md) |
| W9 | [`W9-bundle-decouple/README.md`](W9-bundle-decouple/README.md) | (single-phase wave) |

**Queue-level follow-ups (next tasks):** [`FOLLOWUPS.md`](FOLLOWUPS.md) — review
parking lot (status sync, `.DS_Store`, dist policy, anatomy completeness).

---

## Progress — Ira QA view

> **Lera's extension, applied to a Vue migration.** One row per AF4
> component (44 total), grouped by screen. `baseline` = golden screenshot
> captured in W0-P1 (the "before"). `verified` = W6 confirms the Vue 3 render
> matches that baseline (the "after"). `Note` = any visual/behaviour delta or
> gotcha found. `Preview` = how the reviewer reaches the screen (AF4 is a
> wizard driven by `step` state, not URL routes).
>
> Fill `✓` as each is captured / verified. Mirror this state into
> [`inventory.tsv`](inventory.tsv) (`baseline` / `verified` / `note` columns).

### Entry & path

| Component | Preview (how to open) | baseline | verified | Note |
|---|---|:---:|:---:|---|
| `App.vue` | initial mount on `#activity-finder` | | | root render `#activity-finder-app` |
| `steps/SelectPath.vue` | first screen — path chooser | | | |
| `components/WizardBar.vue` | top bar during wizard steps | | | emits `startOver`, `viewResults` |
| `components/steps/Step.vue` | wrapper for each wizard step | | | uses slots |

### Wizard steps

| Component | Preview (how to open) | baseline | verified | Note |
|---|---|:---:|:---:|---|
| `steps/SelectActivities.vue` | wizard → Activities | | | |
| `steps/SelectAges.vue` | wizard → Ages | | | BootstrapVue consumer (W4-P3) |
| `steps/SelectDays.vue` | wizard → Days | | | |
| `steps/SelectDaysTimes.vue` | wizard → Days & Times | | | |
| `steps/SelectLocations.vue` | wizard → Locations | | | |
| `steps/SelectTimes.vue` | wizard → Times | | | |
| `steps/SelectWeeks.vue` | wizard → Weeks | | | |

### Results

| Component | Preview (how to open) | baseline | verified | Note |
|---|---|:---:|:---:|---|
| `components/Results.vue` | `step === 'results'` | | | uses slots |
| `components/ResultsList.vue` | results list body | | | |
| `components/ResultsBar.vue` | results top bar | | | BootstrapVue consumer (W4-P4) |
| `components/AvailableSpots.vue` | spot badge on each result | | | |
| `components/NoResults.vue` | empty result set | | | |
| `components/Loading.vue` | spinner during fetch | | | |

### Modals

| Component | Preview (how to open) | baseline | verified | Note |
|---|---|:---:|:---:|---|
| `modals/Modal.vue` | base modal shell | | | BootstrapVue consumer (W4-P0) — others depend on it |
| `modals/ActivityDetails.vue` | result → "More info" | | | |
| `modals/Filters.vue` | results → Filters (mobile) | | | |
| `modals/BookmarkFeature.vue` | bookmark intro modal | | | |
| `modals/BookmarkFeatureDescription.vue` | bookmark help text | | | |
| `modals/BookmarkedItems.vue` | view bookmarked list | | | uses slots |

### Filters

| Component | Preview (how to open) | baseline | verified | Note |
|---|---|:---:|:---:|---|
| `filters/Filters.vue` | filters container | | | |
| `filters/Activities.vue` | filter: activities | | | |
| `filters/Ages.vue` | filter: ages | | | BootstrapVue consumer (W4-P3) |
| `filters/Days.vue` | filter: days | | | |
| `filters/DaysTimes.vue` | filter: days & times | | | |
| `filters/Times.vue` | filter: times | | | |
| `filters/Weeks.vue` | filter: weeks | | | |
| `filters/Durations.vue` | filter: durations | | | |
| `filters/StartMonths.vue` | filter: start months | | | |
| `filters/Locations.vue` | filter: locations | | | |
| `filters/InMemberships.vue` | filter: in-membership toggle | | | |
| `filters/SearchForm.vue` | keyword search box | | | `v-model` on App |
| `filters/SortSelect.vue` | sort dropdown | | | |
| `filters/SortRadios.vue` | sort radios | | | |
| `filters/Pager.vue` | results pager | | | |
| `filters/DaxkoPager.vue` | Daxko-source pager | | | |

### Shared atoms

| Component | Preview (how to open) | baseline | verified | Note |
|---|---|:---:|:---:|---|
| `components/Fieldset.vue` | reused in filters/steps | | | BootstrapVue consumer (W4-P1) |
| `components/Foldable.vue` | collapsible section | | | BootstrapVue consumer (W4-P2) |
| `components/FoldableInput.vue` | collapsible input | | | BootstrapVue consumer (W4-P2) |
| `components/AgeIcon.vue` | age glyph | | | |
| `components/BookmarkIcon.vue` | bookmark glyph | | | FontAwesome (W5-P4) |

## Inventory

See [`inventory.tsv`](inventory.tsv) for one-row-per-phase machine-readable
queue state, including the `baseline` / `verified` / `note` columns from
Lera's extension.
