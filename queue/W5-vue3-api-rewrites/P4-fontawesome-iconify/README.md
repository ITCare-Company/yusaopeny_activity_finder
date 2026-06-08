# P4 — FontAwesome v3 + Iconify swap

## Goal

Bump `@fortawesome/vue-fontawesome` to the Vue 3 major (`^3`) and replace
`@iconify/vue2` with `@iconify/vue`, keeping every rendered icon identical.

## Files

- `openy_af4_vue_app/package.json` — icon deps (W1-P2 versions).
- `openy_af4_vue_app/src/main.js` — `library.add([...])`,
  `app.component('font-awesome-icon', FontAwesomeIcon)`.
- Iconify consumers — grep `@iconify/vue2` usages.
- Icon-rendering components, e.g. `components/BookmarkIcon.vue`,
  `components/AgeIcon.vue`.

## Steps

1. Install `@fortawesome/vue-fontawesome ^3` (Vue 3 build); confirm
   `fontawesome-svg-core` + `free-solid-svg-icons` majors are compatible.
2. Update the `FontAwesomeIcon` import/registration if the v3 API differs;
   keep the `library.add([...])` icon set identical (`faFilter`, `faCalendar`,
   … — the 10 icons in `main.js`).
3. Replace `@iconify/vue2` import sites with `@iconify/vue` (component import
   name may change: `Icon`); keep the same icon names/props.
4. Confirm every icon renders at the same size/color/position vs baseline.

## Tests

```sh
cd openy_af4_vue_app && npm run lint && npm run build
grep -rn "@iconify/vue2" src   # must return nothing
```

Harness: screens with FA icons (filter/calendar/clock/chevron/bookmark) and any
Iconify icons; compare vs baseline.

## Validation

Owner approves. Icons render identically; no `@iconify/vue2` references; FA v3
registered.

## Out of scope

- Other API rewrites (P0–P3, P5).

## Result

(to be filled when phase ships)

FontAwesome on v3, Iconify on `@iconify/vue`; icon parity verified.
