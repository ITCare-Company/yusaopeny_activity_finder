# W3 — Decisions

| # | Decision | Why | Source |
|---|---|---|---|
| _ex_ | _(none yet)_ | | |

Candidate decisions this wave may surface:

- Router: dropped vs migrated to v4 (P1) + the `$route` evidence.
- Whether the filter shim is impractical and W5-P0 must run before boot smoke
  (P2) — if so, record the re-ordering and update `inventory.tsv` `blocked_by`.
- Any `@vue/compiler-sfc` template incompatibility found at build (P0).
