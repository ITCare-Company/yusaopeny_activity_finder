# P4 — Seed demo content (baseline data)

## Goal

Seed the demo programs/sessions/branches AF4 renders, so the **Solr** and
**DB** backends have real data and the **sandbox baseline (W0-P1)** is
reproducible. The Mock backend (P2) needs no content; this phase is for the
real backends and the baseline site.

## Files

- Documentation only in this phase folder (`findings.md` — the exact migrate
  commands + node ids produced). No source change.

## Steps

1. Enable the demo modules (sessions + dependencies):
   ```sh
   ddev drush pm:enable openy_demo_nsessions -y
   ```
2. Import demo sessions with dependencies (creates programs, sessions,
   branches, etc.):
   ```sh
   ddev drush migrate:import openy_demo_node_session_01 --execute-dependencies -y
   ```
3. For the **Solr** backend: index the content
   (`ddev drush search-api:index`) per the project SOLR SETUP. For the **DB**
   backend: no indexing — entities are query-ready.
4. Record the produced node ids + the AF4 landing path in `findings.md` so
   W0-P1 can screenshot deterministic screens.

## Tests

```sh
ddev drush migrate:status | grep openy_demo_node_session   # imported
# AF4 (backend = db or solr) lists the seeded programs/sessions.
```

## Validation

Owner approves. Demo content seeded; Solr indexed (if used) or DB query-ready;
`findings.md` records the fixed content set the baseline screenshots target.

## Out of scope

- Mock fixtures (P2 owns its own static data).
- Production content. Demo seed is for sandbox/dev/QA only.

## Result

Shipped (PR #4). Two demo pages: the **Layout Builder** demo
(`lb_activity_finder_demo` migration → `landing_page_lb` with the AF4 block,
backend **mock**) and the existing **paragraph** demo (backend **solr**, depends
on the Solr submodule). The Solr demo-session recipe (enable `openy_node_news`
first, then `openy_demo_nsessions`; `migrate:import openy_demo_node_session_01`;
index) is documented in the Solr submodule README. Verified on a fresh `small_y`:
mock and solr both return count 31.
