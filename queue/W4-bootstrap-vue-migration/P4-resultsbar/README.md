# P4 — Replace BootstrapVue in `ResultsBar.vue`

## Goal

Replace the BootstrapVue widget(s) in `ResultsBar.vue` (the results-screen top
bar — likely a dropdown/modal trigger for search + filter), preserving the
slot-driven search/filter triggers and markup.

## Files

- `openy_af4_vue_app/src/components/ResultsBar.vue`
- Note: `App.vue` passes `v-slot:search` / `v-slot:filter` scoped slots with a
  `hideModal` callback into `ResultsBar` — preserve that scoped-slot contract.

## Steps

1. Enumerate the `b-*` usage and how the `hideModal` scoped-slot callback is
   wired (it likely closes a `b-modal`/`b-dropdown`).
2. Implement the replacement keeping the scoped slots (`search`, `filter`) and
   the `hideModal` callback semantics intact, plus root classes.
3. Verify the search box and filter panel open/close from the bar identically.

## Tests

```sh
cd openy_af4_vue_app && npm run lint && npm run build
```

Harness: on the results screen, open search and filter from the bar; confirm
`hideModal` closes them and markup matches baseline.

## Validation

Owner approves. Scoped-slot `search`/`filter` + `hideModal` behaviour
preserved; markup matches baseline.

## Out of scope

- Other consumers (P0–P3).
- The `Filters` / `SearchForm` bodies (QA'd in W6).

## Result

DONE 2026-06-09.

`ResultsBar.vue` off BootstrapVue; scoped-slot + `hideModal` contract verified.
