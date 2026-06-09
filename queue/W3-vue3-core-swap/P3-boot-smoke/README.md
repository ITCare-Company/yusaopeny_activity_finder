# P3 — Boot smoke

## Goal

Confirm the Vue 3 app mounts and renders its root without boot-time console
errors. This is the wave's exit gate — broken sub-widgets (BootstrapVue,
filters) are expected and tracked, but the app must not white-screen.

## Files

- No source change expected — this is verification. Small boot fixes allowed
  if needed (record them).

## Steps

1. Build (`npm run build`) and mount AF4 in the W0-P1 harness (stub
   `drupalSettings.path.baseUrl`, `Drupal.t`, `Drupal.formatPlural`).
2. Load the initial screen. Confirm:
   - `#activity-finder` mounts and `#activity-finder-app` renders.
   - `App.vue` shows the entry screen (`SelectPath`) or the error alert path.
   - No boot-time exceptions in console (filter/BootstrapVue warnings are
     expected and listed, not fixed here).
3. List every console error/warning and tag each with its owning wave (W4
   BootstrapVue / W5 filters / other). This list seeds W4 + W5 verification.

## Tests

```sh
cd openy_af4_vue_app && npm run build
# load in harness, open devtools console, screenshot the entry screen
```

## Validation

Owner approves. App mounts; entry screen renders; the console-error inventory
is captured and each item is assigned to W4 or W5.

## Out of scope

- Fixing BootstrapVue widgets (W4) or filters (W5).

## Result

DONE 2026-06-09. Boot smoke PASSED on af4-migration.ddev.site.

- `#activity-finder` mounts ✅
- 31 Mock sessions render ✅
- Filters (SCHEDULES, ACTIVITIES, LOCATIONS) visible ✅
- Modal opens on session title click ✅ (BootstrapVue b-modal still works via compat)
- Screenshots: `queue/W3-vue3-core-swap/screenshots/`

Expected console warnings (not errors):
- `[Vue compat]` deprecation notices for filter/mixin/use APIs — routed to W5-P0, W5-P1, W4
- `[BootstrapVue]` — routed to W4
- `getBoundingClientRect` on sticky nav — pre-existing theme issue, unrelated to AF4

No boot-time crashes. W3 gate passed.
