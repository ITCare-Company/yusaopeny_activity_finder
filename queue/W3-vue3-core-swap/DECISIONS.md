# W3 — Decisions

| # | Decision | Why | Source |
|---|---|---|---|
| W3-D1 | Router **dropped** (not migrated to v4) | `routes = []`, zero `$route`/`$router` in src. AF4 drives screens via `step` state in App.vue — router was vestigial. | grep evidence, P1 |
| W3-D1a | W3-D1 evidence **partially refuted** — drop stands, grep was wrong | `App.vue` did use `$route`/`$router` in 3 places (watcher, `getDataFromUrl`, `updateUrl`); the grep missed them. Replaced with native History API in PR #11. Remaining edge cases tracked in [P6](P6-bug-url-state-edge-cases/README.md). | PR #11 |
| W3-D2 | `@vue/compat MODE:2` for W3–W4 | 103 filter pipes in templates — Vue 3 compiler rejects `\| t` syntax without compat mode. Compat enables gradual migration; removed when W5 filter rewrite is complete. | template compilation attempt |
| W3-D3 | `@fortawesome/vue-fontawesome` bumped `^2→^3.0.3` in W3 (not W5) | Peer dep conflict: v2 requires `vue~2`, blocked `npm install` with Vue 3. API surface unchanged — only compatibility wrapper. | npm install ERESOLVE |
| W3-D4 | Filter shim via `app.filter()` is practical (W5-P0 not a gate blocker) | `@vue/compat` MODE:2 supports `app.filter()` and template filter pipes at runtime. Boot smoke passed without pre-running W5-P0. | boot smoke test |
