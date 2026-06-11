# P7 — fetch error + config parity (follow-up to P6)

## Goal

Close the gap between what P6 (`axios → fetch`) **specified** and what shipped.
The P6 spec required the `fetch` wrapper to "explicitly reject/throw on
`!response.ok`" and to preserve axios config (base URL, headers,
credentials). The landed `src/client/index.js` parses `res.json()`
unconditionally — `fetch` does **not** reject on 4xx/5xx, so error responses
silently flow through as `data`. This phase makes the transport faithful to
the axios behaviour callers were written against.

## Why

`axios` rejected the promise on any non-2xx status; every caller's `.catch`
(loading-error UI, no-results fallback) depended on that. Native `fetch`
resolves on 500/404, so:

- a Drupal 500 returns HTML → `res.json()` throws an opaque `SyntaxError`
  instead of a handled HTTP error;
- a JSON error body (`{message: …}`) is handed to components **as if it were
  results data** — wrong-shape render, no error state.

Also unverified vs the original `axios.create({...})`: whether it set
`withCredentials`, a CSRF/`X-CSRF-Token` header, or a timeout. `fetch`
defaults drop all of these. For the read-only `GET af/get-data` path this is
likely benign, but it must be **checked against the Vue 2 source**, not
assumed (per `feedback_no_fabricated_behavior`).

## Files

- `openy_af4_vue_app/src/client/index.js` — add the `!res.ok` guard; surface
  the same error shape axios produced; restore any config the original
  instance set (credentials / headers / timeout) that AF4 actually relied on.
- `openy_af4_vue_app/src/components/Results.vue`, `NoResults.vue`,
  `Loading.vue` (and any `.catch` on `client(...).request(...)`) — confirm the
  error branch still fires with the new reject path.

## Steps

1. Recover the **original** `axios.create({...})` config from git history
   (`git show <pre-W5-P6>:openy_af4_vue_app/src/client/index.js`). List every
   option it set.
2. In the `fetch` wrapper: after `fetch(url)`, `if (!res.ok) throw` an error
   carrying `res.status` + body text, matching the shape `.catch` handlers
   read.
3. Re-apply only the config options the original genuinely used
   (`credentials`, headers). Do **not** invent options the source never set.
4. Add a timeout via `AbortController` **only if** the axios instance had one.
5. Smoke an induced 500 (stop the backend / hit a bad route) and confirm the
   loading-error / no-results UI renders instead of a crash or garbage data.

## Tests

```sh
cd openy_af4_vue_app && npm run lint && npm run build
```

Smoke: load results normally (unchanged), then force a non-200 from
`af/get-data` and confirm the error branch renders — not a white screen, not
results built from an error body.

## Validation

Owner approves. `!res.ok` rejects; error UI fires on 4xx/5xx; only config the
Vue 2 source actually set is restored; happy path byte-identical to P6.

## Out of scope

- Changing endpoints/payloads — error-parity + config-parity only.
- Retry/backoff policy (not in the axios original).

## Result

(to be filled when phase ships)
