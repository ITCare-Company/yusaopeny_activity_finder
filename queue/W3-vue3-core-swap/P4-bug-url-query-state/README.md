# P4 — BUG: interactive URL query state dead after router drop

**Type:** regression (Vue 3 migration) · **Severity:** functional · **Found:** post-merge on the AF4 sandbox

## Symptom

The interactive URL query string no longer works. As the user changes AF4 state
(filters, step, sort, page, locations…) the query params **do not update**, and
**reloading the page does not reproduce the prior state** — it resets to defaults.
Sharing a deep-link URL likewise no longer restores the selection.

## Root cause (verified)

`App.vue` still drives the whole URL ⇄ state sync through `vue-router`:

- `App.vue:705-710` — a `$route` watcher (`immediate: true`) calls `getDataFromUrl()`
  to hydrate state from the query on load and on every URL change.
- `App.vue:814-815` — `getDataFromUrl()` reads `this.$route.query`.
- `App.vue:853-868` — `updateUrl()` writes state back via `this.$router.push({ query })`.

W3 of the migration **dropped vue-router** — `W3-vue3-core-swap/DECISIONS.md` (W3-D1)
states *"router dropped … routes [], zero `$route`/`$router` usage"*. That claim is
**factually wrong**: `App.vue` uses `$route` (×2) and `$router` (×1). On Vue 3 with no
router registered, `this.$route` / `this.$router` are `undefined`:

- the `$route` watcher never fires → `getDataFromUrl()` is not run on load → **state is
  not restored from the URL after reload**;
- `updateUrl()` → `this.$router.push(...)` hits `undefined` → throws / no-ops → **the
  query string is never updated during interaction**.

Both directions of the deep-link/state-sync are therefore dead.

## Classification

**Regression introduced by the Vue 3 migration (W3 router drop).** On Vue 2 this worked
via vue-router. Confirm by comparing the reference (Vue 2) vs new (Vue 3) sandbox:

- Reference (Vue 2): https://sandbox-carnation-cus-d9.y.org/activity-finder-v4
- New (Vue 3): https://af4-vue3.itcaresolutions.org/activity-finder-v4 and
  https://af4-vue3.itcaresolutions.org/activity-finder-v4-layout-builder

Repro: apply a filter / advance a step → check the address bar updates → reload →
expect the state to persist. On Vue 3 it does not.

## Fix direction (not implemented here)

vue-router was intentionally removed (W3-D1) for a single-mount app, so do **not**
re-add it. Re-implement the query sync with the native History API:

- read: parse `window.location.search` (`URLSearchParams`) in place of `this.$route.query`
  (`getDataFromUrl`), run it on `created()`/`mounted()` and on `popstate`;
- write: `window.history.replaceState`/`pushState` with the rebuilt query string in place
  of `this.$router.push({ query })` (`updateUrl`);
- drop the `$route` watcher; trigger `getDataFromUrl()` explicitly on init + `popstate`.

Owning wave: W3 (router) / W5 (API rewrites). Verify against the Vue 2 source so the
restored params and array serialization (`a,b,c`) match exactly.

## Out of scope

- Re-introducing vue-router.
- Any unrelated W6 visual findings.
