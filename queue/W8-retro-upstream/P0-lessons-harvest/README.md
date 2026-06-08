# P0 — Lessons-learned harvest

## Goal

Capture what actually happened across W0–W7 — where reality diverged from the
plan — into a single retro doc, so P1/P2 can feed it upstream without
re-reading every `DECISIONS.md`.

## Files

- Output: `findings.md` in this phase folder.
- Sources: every wave `DECISIONS.md`, each phase `## Result`, the PR #1 review
  thread, the W6 QA notes.

## Steps

1. Walk each wave's `DECISIONS.md` + phase `Result` blocks; pull every entry
   where the outcome differed from the original plan or surprised us. Candidate
   areas (from the queue's own risk notes):
   - Build tool chosen (vue-cli 5 vs Vite) and why (W1-P0 / W2).
   - BootstrapVue replacement actually used per file (W1-P1 / W4).
   - Vue-global version collision resolution — bundled Vue 3 vs `window.Vue3`
     external (W1 D4 / W3).
   - Bootstrap 4 vs 5 CSS parity outcome (W1-P1 / W4).
   - `@vue/compat` — used as a crutch or skipped (W3).
   - Filter→method argument-order gotchas, esp. `formatPlural` (W5-P0).
   - Router dropped vs kept (W3-P1).
   - Any Drupal-side contract change that proved unavoidable (W0-P0 / W7).
2. For each lesson, classify: **universal** (applies to any framework
   migration) vs **AF4-specific**. Universal ones are P1 candidates.
3. Note any anatomy step that was missing or mis-ordered for this migration
   (feeds P1's anatomy-doc enrichment).

## Tests

None — documentation phase. Exit: `findings.md` covers every wave and tags each
lesson universal/specific.

## Validation

Owner reviews `findings.md`. Confirm it is honest (records what went wrong, not
just what worked) and that universal lessons are flagged for P1.

## Out of scope

- Editing the upstream doc (P1) or importing the queue (P2).

## Result

(to be filled when phase ships)

`findings.md` shipped; lessons tagged universal vs AF4-specific.
