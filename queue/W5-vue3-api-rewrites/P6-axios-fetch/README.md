# P6 — Replace axios with native fetch

## Goal

Drop the `axios` dependency in favour of the native `fetch` API. Removes a
runtime dependency and its Drupal-library external, with no change to the API
contract AF4 speaks to the backend.

## Why

`axios` is used in a **single file** (`src/client/index.js` — `import axios`
+ one `axios.create({...})` instance) and is delivered as a build **external**
(`vue.config.js` → `externals.axios`) backed by the Drupal library
`openy_system/axios` (`libraries.yml`). Native `fetch` covers every call AF4
makes; removing axios shrinks the dependency surface and one
`openy_system/*` coupling.

## Files

- `openy_af4_vue_app/src/client/index.js` — replace the axios instance
  (`axios.create({ baseURL, ... })` + its `.get`/`.post` calls) with a small
  `fetch` wrapper preserving the same `baseURL` (`window.drupalSettings.path.baseUrl`),
  headers, and response shape the callers expect.
- `openy_af4_vue_app/package.json` — remove `axios` (dependency removal is
  destructive-first per RULES).
- `openy_af4_vue_app/vue.config.js` — remove `axios` from `externals`.
- `openy_activity_finder.libraries.yml` — remove the `openy_system/axios`
  dependency from the `activity_finder_4` library **only after** confirming no
  other AF4 code path needs it. (Consumer-contract change → record in W5
  `DECISIONS.md`; conditional, like the Vue-runtime-delivery decision.)

## Steps

1. Inventory every call on the axios instance in `client/index.js` (methods,
   params, response handling, error handling).
2. Write a minimal `fetch` wrapper: same base URL, JSON parsing, error
   surfacing equivalent to the axios behaviour the callers rely on (status →
   reject, `.data` → parsed body).
3. Swap call sites; keep the module's exported function signatures identical so
   callers do not change.
4. Remove `axios` from `package.json` (confirm separately) + `vue.config.js`
   externals; reconcile `libraries.yml` axios dep.
5. Build + smoke the data-loading screens (results, session detail) — same
   requests, same rendered data.

## Tests

```sh
cd openy_af4_vue_app && npm run lint && npm run build
grep -rn "axios" src   # must return nothing
```

Smoke: load results + a session detail; confirm the network requests + rendered
data are unchanged from the axios version.

## Validation

Owner approves. Zero `axios` references; `fetch` wrapper preserves base URL,
JSON, and error semantics; data screens render identically; `package.json` /
`vue.config.js` / `libraries.yml` reconciled.

## Out of scope

- Changing the backend endpoints or payloads (`af/get-data`,
  `af/api/v1/session-data`, `af/more-info`) — transport swap only.

## Result

(to be filled when phase ships)

axios removed; native fetch in `client/index.js`; external + library dep
reconciled; data screens unchanged.
