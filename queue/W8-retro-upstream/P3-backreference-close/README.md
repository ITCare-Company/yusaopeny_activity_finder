# P3 — Back-reference + close

## Goal

Close the loop: point this queue at the upstream anatomy as its methodology
source, mark the queue complete, and close ITCR-1273.

## Files

- `queue/README.md` — "Pinned references": add the upstream
  `MIGRATION-QUEUE-ANATOMY.md` as the methodology source.
- `queue/INDEX.md` — mark all waves done; final status pass.
- `queue/inventory.tsv` — flip remaining rows to `done`; keep 11 columns
  (run the pad/detect check from the anatomy doc Part 2A before committing).
- ITCR-1273 — final comment + transition.

## Steps

1. Add a "methodology source" line to `README.md` pinned references linking the
   merged upstream anatomy doc (PR #80) and the P1/P2 PRs.
2. Reconcile `INDEX.md` + `inventory.tsv` so every wave/phase reads its final
   state; verify TSV column uniformity:
   ```sh
   awk -F'\t' '{c[NF]++} END{for(k in c) print k" cols: "c[k]" rows"}' queue/inventory.tsv
   ```
3. Comment on ITCR-1273 with: PR #1 (migration), the upstream PRs (P1/P2), and
   the PKB entry path. Transition the ticket to its done state.
4. Confirm the AF4 queue, the upstream anatomy doc, and the PKB entry all
   cross-reference each other.

## Tests

TSV uniform-column check green; all INDEX statuses `done`.

## Validation

Owner approves. The three artifacts (this queue, the anatomy doc, the PKB
entry) form a closed reference loop; ITCR-1273 closed.

## Out of scope

- Any further migration work — the epic is complete.

## Result

(to be filled when phase ships)

Methodology back-reference added; queue marked complete; ITCR-1273 closed;
reference loop verified.
