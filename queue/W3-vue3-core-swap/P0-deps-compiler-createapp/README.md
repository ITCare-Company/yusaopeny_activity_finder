# P0 — Vue 3 deps + compiler + createApp

## Goal

Install Vue 3, swap the SFC compiler, and rewrite the app bootstrap in
`src/main.js` from `new Vue(...).$mount()` to `createApp(App).mount()`,
preserving the mount id and the GA event bridge.

## Files

- `openy_af4_vue_app/package.json` — `vue ^3`, `@vue/compiler-sfc`, remove
  `vue-template-compiler` (W1-P2 versions).
- `openy_af4_vue_app/src/main.js` — bootstrap rewrite.
- Build config (vue.config.js / vite.config.js) — point SFC compilation at the
  Vue 3 compiler if the build tool needs it.

## Steps

1. Update deps to the locked Vue 3 versions; remove `vue-template-compiler`.
2. Rewrite the bootstrap (per `MIGRATION-REFERENCE.md` §1):
   ```js
   import { createApp } from 'vue'
   import App from '@/App.vue'
   import router from '@/router/index.js'
   const app = createApp(App)
   app.use(router)                       // router handled in P1
   app.component('font-awesome-icon', FontAwesomeIcon)   // FA bump in W5-P4
   app.mount('#activity-finder')
   ```
3. **Preserve** the GA listener block
   (`document.addEventListener('openy_activity_finder_event', …)`) verbatim —
   it is framework-agnostic.
4. Drop `Vue.config.productionTip` / `Vue.config.devtools` (removed in Vue 3).
5. Leave `Vue.use(BootstrapVue)`, `Vue.filter(...)`, `Vue.mixin(...)` as
   **temporary** `app.*` shims or commented stubs so the app compiles — the
   real rewrites are W4 (BootstrapVue), W5 (filters/mixin). Note each shim with
   a `// TODO(W4/W5)` so nothing is silently left behind.
6. Confirm the mount preserves `#activity-finder` (contract) and `App.vue`
   still renders `#activity-finder-app`.

## Tests

```sh
cd openy_af4_vue_app
npm ci && npm run build       # must compile under Vue 3 + new compiler
```

Boot in the harness — expect the app to mount; sub-widgets using BootstrapVue
or filters may error (tracked for W4/W5), but the root must render.

## Validation

Owner approves. Build compiles on Vue 3; bootstrap rewritten; mount id
preserved; GA bridge intact; every temporary shim carries a `TODO(W4/W5)`.

## Out of scope

- Router (P1), global API cleanup (P2), BootstrapVue (W4), filters (W5).

## Result

(to be filled when phase ships)

Vue 3 installed, compiler swapped, `createApp` bootstrap in place; shims noted;
build green. Any compiler surprise logged in W3 `DECISIONS.md`.
