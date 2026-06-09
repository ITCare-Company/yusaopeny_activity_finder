# W1 — Decisions

The three locked choices that govern W2–W5. Fill on approval; later waves cite
these and must not re-decide.

## D1 — Build tool (P0)

- **Decision:** **Vite** (library mode)
- **Build command:**
  ```
  vite build
  ```
- **vite.config.js sketch:**
  ```js
  build: {
    lib: {
      entry: 'src/main.js',
      name: 'activity_finder_4',
      formats: ['umd'],
      fileName: () => 'activity_finder_4.umd.min.js'
    },
    rollupOptions: {
      // D4: Vue 3 bundled — no externals for vue/vue-router
      output: { assetFileNames: 'activity_finder_4.[ext]' }
    }
  },
  css: {
    preprocessorOptions: {
      scss: { additionalData: '@use "./src/scss/global.scss";' }
    }
  }
  ```
- **UMD-contract preservation:** same output path `dist/activity_finder_4.umd.min.js` + `dist/activity_finder_4.css` — libraries.yml untouched.
- **Why:** vue-cli in maintenance mode; Vite is the Vue 3 recommended tool with active development.

## D2 — BootstrapVue replacement (P1)

- **Decision (overall):** **Hand-rolled** local components. Zero new dependencies. Bootstrap 4 markup preserved.
- **Per-consumer:**
  | File | BV usage | Strategy |
  |---|---|---|
  | `modals/Modal.vue` | `<b-modal>` | Local `AppModal.vue` — Bootstrap 4 modal markup + `v-show` + `@keydown.esc` |
  | `Fieldset.vue` | `v-b-toggle` + `<b-collapse>` | Replace with `@click="open = !open"` + `<div v-show="open">` + CSS transition |
  | `Foldable.vue` | `v-b-toggle` + `<b-collapse>` | Same pattern as Fieldset |
  | `FoldableInput.vue` | `v-b-toggle` + `<b-collapse>` | Same pattern as Fieldset |
  | `filters/Ages.vue` | `<b-tooltip>` on disabled item | Native `title` attribute on wrapper |
  | `steps/SelectAges.vue` | `<b-tooltip>` on disabled item | Native `title` attribute on wrapper |
- **Why:** bootstrap-vue-next requires Bootstrap 5 CSS — conflicts with BS4 theme on YMCA sites. Hand-rolled keeps BS4 classes intact → W0 visual baseline preserved.

## D3 — Ecosystem version lock (P2)

| Package | From | To | Action |
|---|---|---|---|
| `vue` | `^2.6.14` | `^3.4` | bump (W3) |
| `vue-router` | `^3.5.3` | `^4.4` | bump (W3) |
| `vue-template-compiler` | `^2.6.14` | — | remove (W3) |
| `@vue/compiler-sfc` | — | `^3.4` | add (W3) |
| `bootstrap-vue` | `^2.22.0` | — | remove (W4, D2) |
| `@fortawesome/vue-fontawesome` | `^2.0.6` | `^3.0` | bump (W5) |
| `@iconify/vue2` | `^2.1.0` | `@iconify/vue ^4.1` | replace package name (W5) |
| `@vue/cli-service` | `^4.5.13` | — | remove (W2, D1) |
| `vite` | — | `^5.0` | add (W2, D1) |
| `@vitejs/plugin-vue` | — | `^5.0` | add (W2) |
| `eslint-plugin-vue` | `^5.0.0` | `^9.0` | bump (W5, Vue 3 rules) |
| `eslint` | `^5.16.0` | `^8.x` | bump (needed for eslint-plugin-vue 9) |

- **Why:** Locked to prevent re-deciding per wave. W2 handles build tooling; W3 handles Vue core; W4 handles BV; W5 handles remaining APIs.

## D4 — Bundled vs. External Vue 3 core

- **Decision:** **Bundle Vue 3 + Vue Router 4 inside the UMD.** Remove `openy_system/vue` and `openy_system/vue-router` from `activity_finder_4` library dependencies in `openy_activity_finder.libraries.yml` (W7).
- **Why:** AF3 and Camp Finder use global `window.Vue` (Vue 2). Bundling Vue 3 avoids runtime conflict — AF4 runs its own isolated Vue 3 instance. External approach would require new `openy_system/vue3` entry and coordination with all consumers.

