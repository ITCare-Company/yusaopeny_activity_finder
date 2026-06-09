# P1 — vue-router 3 → 4

## Goal

Migrate `src/router/index.js` to vue-router 4, or drop the router entirely if
the W0-P2 audit confirmed it is vestigial.

## Files

- `openy_af4_vue_app/src/router/index.js`
- `openy_af4_vue_app/src/main.js` (`app.use(router)`)
- `openy_af4_vue_app/package.json` (`vue-router ^4`)

## Steps

1. Recall the W0-P2 finding: `routes` is empty and AF4 drives screens via the
   `step` state in `App.vue`. Confirm whether any component reads `$route` /
   `$router`.
2. **If the router is unused:** remove `vue-router`, delete
   `src/router/index.js`, drop `app.use(router)`. Simplest, smallest surface.
3. **If the router is needed** (e.g. `history` side effects, deep links):
   migrate to:
   ```js
   import { createRouter, createWebHistory } from 'vue-router'
   const router = createRouter({ history: createWebHistory(), routes: [] })
   ```
   Remove `Vue.use(VueRouter)`.
4. Record which path was taken in W3 `DECISIONS.md` (drop vs migrate) with the
   evidence (`$route` grep result).

## Tests

```sh
cd openy_af4_vue_app && npm run build
```

Boot smoke — navigation/back-button behaviour (if any) unchanged vs baseline.

## Validation

Owner approves. The drop-vs-migrate decision is evidence-backed and logged.
Build green.

## Out of scope

- Adding new routes (AF4 has none).

## Result

DONE 2026-06-09. **Decision: REMOVED.**

Evidence: `grep -rn '\$route\|\$router' src/` → zero results. `routes = []` in index.js.
Router was vestigial — AF4 drives screens via `step` state in App.vue.
`src/router/index.js` deleted; `app.use(router)` removed from main.js.
`vue-router` kept in package.json (still a dep of bootstrap-vue); removed from externals (bundled per D4).
Decision logged in W3 DECISIONS.md.
