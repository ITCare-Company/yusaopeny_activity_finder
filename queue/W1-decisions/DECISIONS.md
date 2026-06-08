# W1 — Decisions

The three locked choices that govern W2–W5. Fill on approval; later waves cite
these and must not re-decide.

## D1 — Build tool (P0)

- **Decision:** _(vue-cli 5 | Vite)_ — pending
- **Build command / config sketch:** _(fill)_
- **UMD-contract preservation:** _(how `activity_finder_4` UMD global + single CSS file are kept)_
- **Why:** _(fill)_

## D2 — BootstrapVue replacement (P1)

- **Decision (overall):** _(bootstrap-vue-next | plain BS5 markup | hand-rolled | mixed)_ — pending
- **Per-consumer:**
  | File | Strategy |
  |---|---|
  | `modals/Modal.vue` | _(fill)_ |
  | `Fieldset.vue` | _(fill)_ |
  | `Foldable.vue` / `FoldableInput.vue` | _(fill)_ |
  | `filters/Ages.vue` / `steps/SelectAges.vue` | _(fill)_ |
  | `ResultsBar.vue` | _(fill)_ |
- **Why:** _(fill)_

## D3 — Ecosystem version lock (P2)

| Package | From | To | Action |
|---|---|---|---|
| `vue` | `^2.6.14` | _(fill)_ | bump |
| `vue-router` | `^3.5.3` | _(fill)_ | bump |
| `vue-template-compiler` | `^2.6.14` | — | remove |
| `@vue/compiler-sfc` | — | _(fill)_ | add |
| `bootstrap-vue` | `^2.22.0` | — | replace (D2) |
| `@fortawesome/vue-fontawesome` | `^2.0.6` | _(fill)_ | bump |
| `@iconify/vue2` | `^2.1.0` | `@iconify/vue` _(fill)_ | replace |
| build tool | `@vue/cli-service ^4.5.13` | _(fill, D1)_ | bump/replace |
| `eslint-plugin-vue` | `^5.0.0` | _(fill)_ | bump |

- **Why:** _(fill)_
