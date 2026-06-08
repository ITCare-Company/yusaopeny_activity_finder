# P0 — Remove template filters

## Goal

Convert the three Vue 2 filters and all ~103 template usages to method calls,
with **byte-identical i18n behaviour** (`window.Drupal.t` / `formatPlural`,
`context: 'Activity Finder'`).

## Files

- `openy_af4_vue_app/src/main.js` — remove `Vue.filter('capitalize'|'t'|'formatPlural')`.
- Every `.vue` template using `| t` (~88), `| formatPlural` (~12),
  `| capitalize` (~3). Enumerate with the W0-P2 grep.

## Steps

1. **`t` and `formatPlural` already exist as mixin methods** — the conversion
   is mechanical:
   - `{{ x | t }}` → `{{ t(x) }}`
   - `{{ n | formatPlural(sing, plur) }}` → `{{ formatPlural(n, sing, plur) }}`
     (mind argument order — the filter passed `value` first implicitly; the
     method takes `value` as the first explicit arg).
   - chained / arg'd filters: rewrite each call faithfully; do not drop the
     `context` option.
2. **`capitalize`** exists only as a filter — add a `capitalize` method (W5-P1
   global properties, or a local helper) and rewrite `{{ x | capitalize }}` →
   `{{ capitalize(x) }}`.
3. Remove the three `Vue.filter(...)` registrations from `main.js`.
4. Grep to confirm **zero** `|` filter usages remain in templates
   (`grep -rnE "\\{\\{[^}]*\\| ?[a-zA-Z]" src` excluding `||`).
5. Spot-check several converted strings render the same translated text.

## Tests

```sh
cd openy_af4_vue_app && npm run lint && npm run build
grep -rnE "\| ?(capitalize|t|formatPlural)\b" src   # must return nothing
```

Harness: load screens with translated labels + pluralized counts (results
count, filter labels); confirm identical text vs baseline.

## Validation

Owner approves. Zero filters remain; translated + pluralized output matches
baseline exactly; `context: 'Activity Finder'` preserved everywhere.

## Out of scope

- The mixin → global-properties move (P1) — except adding `capitalize` if
  that's where it lands.

## Result

(to be filled when phase ships)

Filters removed; ~103 usages converted; i18n parity verified. Any argument-order
gotcha (esp. `formatPlural`) logged in W5 `DECISIONS.md`.
