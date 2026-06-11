# W4 — Decisions

| # | Decision | Why | Source |
|---|---|---|---|
| W4-D1 | `<teleport>` dropped from Modal — plain fixed-position div instead | `@vue/compat MODE:2` doesn't reliably bind event handlers through teleport; close button and backdrop click didn't work. Inline z-index (2050/2040) guarantees stacking without teleport. | modal close bug in testing |
| W4-D2 | `bootstrap-vue` CSS kept (bootstrap package stays) | BS4 base modal CSS (`.modal`, `.modal-dialog`, `.modal-content`, `.modal-backdrop`) still needed for layout. Only BootstrapVue JS removed. | build + visual verification |
| W4-D3 | `b-tooltip` → native `title` attribute | Two uses only (disabled age items); native title sufficient, zero deps, accessible. | W1-D2 strategy |

Candidate decisions this wave may surface:

- Any modal/collapse/form trigger-API change forced by the replacement (P0–P4)
  and which consumers it touched.
- Whether `bootstrap` (CSS) stays after `bootstrap-vue` is removed (depends on
  W1-P1 strategy).
- Any BootstrapVue widget with no clean like-for-like equivalent (escalate to a
  small redesign decision, logged here).
