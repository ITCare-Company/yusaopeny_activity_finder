# P1 — Enrich the upstream anatomy doc

## Goal

PR the universal lessons from P0 into
`template_for_agents/process-knowledge-base/MIGRATION-QUEUE-ANATOMY.md`, so the
next framework migration inherits them. Fulfils the anatomy-upstream-PR rule.

## Files

- Upstream: `process-knowledge-base/MIGRATION-QUEUE-ANATOMY.md` in
  `ITCare-Company/template_for_agents` (seeded by PR #80, merged).
- Local: the P0 `findings.md` is the input.

## Steps

1. In a `template_for_agents` worktree, branch off `main`.
2. For each **universal** lesson from P0, fold it into the matching section of
   the anatomy doc:
   - new wave-skeleton row or invariant → Part 1;
   - new `inventory.tsv` / progress-tracking nuance → Part 2;
   - a best-practice that held → the "Process & best practices" section.
3. If P0 found nothing new, record that explicitly in this phase `Result`
   ("anatomy held for AF4 — no upstream change") — a clean confirmation is a
   valid outcome and still worth noting.
4. Open the PR (per `feedback_pr_body_from_file`); link it back to ITCR-1273
   and to AF4 PR #1.

## Tests

None. Docs PR; reviewer reads it.

## Validation

Owner approves before push. The upstream PR either adds concrete universal
lessons or the phase `Result` justifies "no change needed".

## Out of scope

- Importing the queue as a PKB entry (P2).
- AF4-specific lessons that don't generalise (they stay in this queue's retro).

## Result

(to be filled when phase ships)

Upstream anatomy doc enriched (or "held") via a `template_for_agents` PR; link
recorded here + in W8 `DECISIONS.md`.
