# P5 — Remove BootstrapVue global + prune dependency

## Goal

Once no component imports BootstrapVue, remove the global plugin shim, delete
the dependency, and reconcile any now-dead BootstrapVue CSS.

## Files

- `openy_af4_vue_app/src/main.js` — remove the `Vue.use(BootstrapVue)` /
  `app.use(...)` shim and its import.
- `openy_af4_vue_app/package.json` — remove `bootstrap-vue`.
- SCSS: `src/scss/global.scss`, `_variables.scss` — remove any BootstrapVue
  `@import` / overrides; keep plain `bootstrap` if the replacement still uses
  Bootstrap CSS (per W1-P1).

## Steps

1. Grep `src` for any residual `bootstrap-vue` / `<b-` / `$bvModal` / `v-b-` —
   must be **zero** (P0–P4 cleared them).
2. Remove the global registration + import from `main.js`.
3. Remove `bootstrap-vue` from `package.json`; `npm install` to update the
   lockfile (separate confirmation — dependency removal is destructive-first
   per RULES).
4. **Dead-CSS reconcile** (per the canvas queue's "no dead modifier classes"
   discipline): any SCSS that only existed to override BootstrapVue and now
   matches nothing — delete it or document why it stays.
5. Build and confirm the bundle shrinks (BootstrapVue gone) and renders
   identically.

## Tests

```sh
cd openy_af4_vue_app && npm run lint && npm run build
grep -rE "bootstrap-vue|<b-|\$bvModal|v-b-" src   # must return nothing
```

## Validation

Owner approves (dependency removal confirmed separately). Zero BootstrapVue
references; lockfile updated; no dead BootstrapVue CSS; full app renders vs
baseline.

## Out of scope

- Replacing widgets (done in P0–P4).

## Result

(to be filled when phase ships)

BootstrapVue fully removed; lockfile + bundle updated; dead CSS reconciled;
app parity confirmed.
