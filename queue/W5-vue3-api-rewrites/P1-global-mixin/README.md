# P1 — Global mixin → global properties / composable

## Goal

Replace the global `Vue.mixin({...})` (lifted to a temporary `app.mixin` in
W3-P2) with a Vue 3-idiomatic mechanism: `app.config.globalProperties` and/or
a composable, keeping `t`, `formatPlural`, `trackEvent`, `isIosMobile`,
`getCookie` available to every component exactly as before.

## Files

- `openy_af4_vue_app/src/main.js` — the mixin definition.
- Optionally a new `src/helpers/app-globals.js` (or composable
  `src/composables/useAppGlobals.js`) — record the choice in W5 `DECISIONS.md`.
- Components that call `this.t` / `this.formatPlural` / `this.trackEvent` /
  `this.isIosMobile` / `this.getCookie` — confirm they still resolve.

## Steps

1. Decide the mechanism (record it):
   - **Global properties** (`app.config.globalProperties.t = ...`) — least
     churn, keeps `this.t(...)` working in Options API components (AF4 is
     Options API).
   - **Composable** — cleaner, but requires touching every consumer.
   Given AF4 is Options API throughout, global properties are the low-risk
   default unless an owner prefers composables.
2. Move each member:
   - `isIosMobile` (computed) → global property getter or computed-equivalent.
   - `trackEvent`, `t`, `formatPlural`, `getCookie` (methods) → global
     properties.
3. Remove the `app.mixin` shim from `main.js`.
4. Confirm `t` / `formatPlural` used by W5-P0 still resolve (they must — P0
   depends on these methods existing).

## Tests

```sh
cd openy_af4_vue_app && npm run lint && npm run build
```

Harness: trigger a `trackEvent` (GA dataLayer push), check `isIosMobile`
branch, confirm `getCookie` path — all behave vs baseline.

## Validation

Owner approves. Mixin removed; all five members resolve everywhere; GA
tracking + iOS branch + cookie read unchanged.

## Out of scope

- Filter conversion (P0) — though `t`/`formatPlural` must keep working for it.

## Result

(to be filled when phase ships)

Mixin migrated to _(global properties | composable)_; members verified;
mechanism recorded in W5 `DECISIONS.md`.
