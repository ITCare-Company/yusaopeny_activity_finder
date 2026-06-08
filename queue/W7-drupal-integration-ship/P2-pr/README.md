# P2 — Open PR to the fork

## Goal

Open the review PR for the Vue 3 AF4 migration against the fork's `6.x` branch.

## Files

- No source change. PR description written to a temp file and passed via
  `gh pr create --body-file` (per `feedback_pr_body_from_file`).

## Steps

1. Confirm the wave branch holds the full migration (W2–W7 commits) plus this
   queue scaffold.
2. Write a concise PR body (~80–120 lines per `feedback_pr_body_concise`):
   - what: AF4 Vue 2 → Vue 3 (ITCR-1273);
   - the wave summary (link `queue/INDEX.md`);
   - the BootstrapVue replacement decision (link W1 `DECISIONS.md`);
   - contract-preservation note (UMD global, mount, `libraries.yml`);
   - QA evidence (W6 parity, live-site smoke W7-P1);
   - test plan + how to build (`cd openy_af4_vue_app && npm ci && npm run build`).
3. Create the PR:
   ```sh
   gh pr create --repo ITCare-Company/yusaopeny_activity_finder \
     --base 6.x --title "AF4: Vue 2 → Vue 3 (ITCR-1273)" --body-file /tmp/af4-vue3-pr.md
   ```
4. Link the PR on ITCR-1273. Update `RULES.md` cross-repo record / `INDEX.md`
   status.

## Tests

CI on the PR (lint + build) green.

## Validation

Owner approves before push (per RULES approval gate). PR opened, linked to
ITCR-1273, CI green.

## Out of scope

- Merging (reviewer's call).
- Upstreaming to YCloudYUSA.

## Result

(to be filled when phase ships)

PR opened against `6.x`, linked to ITCR-1273; CI green; queue status updated.
