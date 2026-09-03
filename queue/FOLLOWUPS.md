# Follow-ups — queue-level (yusaopeny_activity_finder AF4 migration)

Scope-discipline parking lot (per queue anatomy: keep each wave lean, capture
cross-cutting nuance here, cite from the phase that picks it up). Per-wave
follow-ups live in that wave's `FOLLOWUPS.md` (e.g. `W0b-backend-harness/`).

These items surfaced in the W0–W6 review (PR #6). Each is a **task for Lera**,
listed smallest-first. Code-fix items already promoted to phases are linked,
not duplicated.

## Bugs (post-merge, found on the sandbox)

- **Interactive URL query state dead** (regression, W3 router drop) → [`W3-vue3-core-swap/P4-bug-url-query-state/`](W3-vue3-core-swap/P4-bug-url-query-state/README.md). AF4 query params no longer update on interaction and a reload no longer reproduces state: `App.vue` still uses `$route`/`$router` (705/815/868) but W3 removed vue-router.

## Promoted to phases (do via the normal phase cycle)

- **fetch error + config parity** → [`W5-vue3-api-rewrites/P7-fetch-error-parity/`](W5-vue3-api-rewrites/P7-fetch-error-parity/README.md)
- **hand-rolled modal a11y** → [`W4-bootstrap-vue-migration/P6-modal-a11y/`](W4-bootstrap-vue-migration/P6-modal-a11y/README.md)
- **esm-bundler CSP review** → [`W7-drupal-integration-ship/P3-csp-runtime-compiler/`](W7-drupal-integration-ship/P3-csp-runtime-compiler/README.md)
- **externalize Vue 3 runtime (revisit W1-D4)** → [`W9-bundle-decouple/P0-externalize-vue3/`](W9-bundle-decouple/P0-externalize-vue3/README.md) — `openy_repeat` MR!14 proved the `openy_system/vue3` externalize path D4 originally lacked; runs post-W7, independent wave.

## Queue hygiene (chore(queue), one nuance per commit)

- **Sync phase statuses.** `INDEX.md` + `inventory.tsv` still show W1–W6 as
  `pending`, but PR #6 shipped them. Flip each shipped phase to `shipped` with
  its commit SHA in `inventory.tsv` (`commit_or_pr` column), per RULES "Queue
  maintenance". The `baseline`/`verified` columns stay empty until Ira runs W6
  — that part is correct. Without this, Lera's own Ira-QA progress tracker
  reports the wrong state.
- **Remove the committed `.DS_Store`.** `queue/W6-qa-visual-functional/.DS_Store`
  is tracked. `.gitignore` only exists inside `openy_af4_vue_app/`; add a
  repo-root (or `queue/`) `.gitignore` with `.DS_Store` and `git rm --cached`
  the tracked one.
- **dist commit policy.** `dist/activity_finder_4.umd.min.js` was committed
  across the migration commits. RULES says `dist/` is regenerated in W7 and a
  hand-edited `dist/` diff is rejected. Decide one: gitignore `dist/*.umd.min.js`
  until W7, **or** assert every committed `dist/` is a clean `npm run build`
  output (and record that in W7-P0). The `.map` is already gitignored — keep it.

## Verify before ship

- **`package-lock.json` churn** (+2488 / −18053 from vue-cli→Vite + dep
  removals). Confirm `npm ci` reproduces the lockfile cleanly and no
  unexpected transitive package was dropped, before W7 ship.
- **v-model migration audit.** 27 components were converted Vue 2 `value`/`input`
  → Vue 3 `modelValue`/`update:modelValue` via a Python script. W6 must
  exercise every two-way binding (filters, search box, sort, modal open state)
  — script-driven edits are the most likely place for a missed edge case.

## Anatomy completeness (optional, raises the queue to canonical shape)

- **Phase-boundary tags.** RULES rule 5 asks for a `v-w<N>-<phase>-ok` git tag
  after each approved phase. None exist yet — start tagging from the next
  phase, or backfill tags on the W0–W6 ship commits.
- **inventory commit-SHA discipline.** Canonical `inventory.tsv` carries the
  ship SHA per phase (WHAT-STATE). Fill `commit_or_pr` as phases land instead
  of leaving it blank.
- **Per-person rules files** (behaviour 10). A short `LERA.md` / `IRA.md` with
  each person's standing preferences would match the canonical personalization
  pattern; currently ownership is only inline tags.
