# P2 — Global API (`Vue.*` → `app.*`)

## Goal

Move the remaining Vue 2 global-API calls in `src/main.js` onto the `app`
instance, leaving only the temporary filter/mixin shims for W5.

## Files

- `openy_af4_vue_app/src/main.js`

## Steps

1. `Vue.component('font-awesome-icon', FontAwesomeIcon)` →
   `app.component('font-awesome-icon', FontAwesomeIcon)`. (The FA package bump
   itself is W5-P4; here only the registration call moves.)
2. `Vue.use(BootstrapVue)` → leave as a clearly-marked shim
   (`// TODO(W4): replace BootstrapVue`); real removal is W4-P5.
3. `Vue.config.*` — already dropped in P0; confirm none remain.
4. `Vue.mixin({...})` → `app.mixin({...})` as a **temporary** lift so methods
   (`t`, `formatPlural`, `trackEvent`, `isIosMobile`, `getCookie`) stay
   available; the proper move to global properties / composable is W5-P1. Mark
   with `// TODO(W5-P1)`.
5. `Vue.filter(...)` — leave registered via a temporary compatibility shim if
   the build still references filters, marked `// TODO(W5-P0)`; real removal is
   W5-P0. (Vue 3 has no `app.filter`; if a shim is impractical, W5-P0 may need
   to run before boot smoke — note this dependency.)
6. Verify no bare `Vue.` reference remains except intentional shims.

## Tests

```sh
cd openy_af4_vue_app && npm run build
```

## Validation

Owner approves. All global API on `app.*`; every remaining shim is labelled
with the wave/phase that removes it; no orphan `Vue.` calls.

## Out of scope

- BootstrapVue removal (W4-P5).
- Filters / mixin proper rewrite (W5-P0, W5-P1).

## Result

(to be filled when phase ships)

Global API lifted to `app.*`; shims labelled. If a filter shim proved
impractical and W5-P0 must precede boot smoke, the gating change is recorded
in W3 `DECISIONS.md` + `inventory.tsv`.
