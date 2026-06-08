# P2 — Subtree-import this queue as a PKB entry

## Goal

Eject the finished `queue/` into `template_for_agents/process-knowledge-base/`
as a full historical entry, so its waves/decisions/phase records join the other
reference queues with original git history preserved.

## Files

- New: `process-knowledge-base/yusaopeny-activity-finder-queue/` (subtree).
- Edit: `process-knowledge-base/README.md` "What lives here" table.
- Edit: `TEMPLATES_INDEX.md` Process Knowledge Base section.

## Steps

1. In the AF4 fork, split the queue prefix:
   ```sh
   git subtree split --prefix=queue -b queue-export
   ```
2. In a `template_for_agents` worktree:
   ```sh
   git remote add tmp /path/to/yusaopeny_activity_finder.git
   git fetch tmp queue-export
   git subtree add --prefix=process-knowledge-base/yusaopeny-activity-finder-queue tmp queue-export
   git remote remove tmp
   ```
3. Register the entry in both indexes — note it as the worked example of the
   **migration-queue anatomy** (cross-link `MIGRATION-QUEUE-ANATOMY.md`).
4. Open the PR (separate from P1's, or stack on the same `template_for_agents`
   branch if still open — per `feedback_one_pr_per_repo`).

## Tests

`git log process-knowledge-base/yusaopeny-activity-finder-queue` shows the
original AF4 queue commits.

## Validation

Owner approves before push. Entry imported with history; both indexes updated;
cross-linked to the anatomy doc.

## Out of scope

- Anatomy-doc enrichment (P1).

## Result

(to be filled when phase ships)

Finished queue imported as a PKB entry + registered; history preserved.
