# W6 — Decisions

| # | Decision | Why | Source |
|---|---|---|---|
| W6-D1 | Modal close button uses `×` char instead of Iconify icon | `@iconify/vue` Icon in Modal.vue was TODO(W5-P4) — replaced with `&times;` to unblock. Visually different but functional. Not a regression to re-verify, it's a known pending item. | W4-P0 |
| W6-D2 | `<b-tooltip>` replaced with native `title` attr | Ages + SelectAges disabled items show browser-native tooltip instead of styled BootstrapVue tooltip. Less styled but functionally equivalent. | W4-P3 / W1-D3 |
| W6-D3 | Modal z-index via inline style (2050/2040), not CSS class | `<teleport>` removed due to Vue 3 compat event binding issues. Backdrop at z-index:2040, dialog at z-index:2050 via inline style. Should match visual baseline. | W4-D1 |

Candidate decisions this wave may surface:

- Any intentional, owner-accepted visual difference vs the Vue 2 baseline
  (record per component so it is not re-flagged as a regression).
- The pixel-diff tolerance agreed with Ira (e.g. ±2px position, exact colour).
- Which regressions routed back to which W4/W5 phase, and re-verify status.
