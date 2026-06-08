# Wave W5 — Vue 3 API rewrites

Clear every remaining Vue 2-ism the boot shims left behind: template filters,
the global mixin, emits/v-model declarations, legacy slots, the
FontAwesome/Iconify bump, and the eslint-plugin-vue upgrade.

**Depends on W3 (app boots). Runs alongside W4** (different files mostly).
**Blocks W6.**

## Phases

| Phase | Goal | Status |
|---|---|---|
| P0 | Remove template filters (~103 pipes) → methods; i18n parity. | pending |
| P1 | Global mixin → global properties / composable. | pending |
| P2 | Declare `emits`, audit `v-model` across 44 components. | pending |
| P3 | Sweep legacy slot syntax. | pending |
| P4 | FontAwesome v2→v3, `@iconify/vue2`→`@iconify/vue`. | pending |
| P5 | eslint-plugin-vue 9 + prettier bump; green lint. | pending |

## Done when

No `Vue.filter` / `| filter` usage remains, the mixin is migrated, emits are
declared, slots are clean, icons are on Vue 3 packages, and `npm run lint`
passes under eslint-plugin-vue 9.

## Decisions

See [`DECISIONS.md`](DECISIONS.md).

## Out of scope

- BootstrapVue (W4).
- Visual QA (W6).

## Onboarding (UA — Lera)

W5 — добиваємо Vue 2-залишки: фільтри (`| t`, `| capitalize`, `| formatPlural`)
→ методи, глобальний mixin → нормальні глобальні властивості, оголошуємо
`emits`, оновлюємо іконки й лінтер. Головне — переклади (`Drupal.t`) мають
працювати точно як було.
