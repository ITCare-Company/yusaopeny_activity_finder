# AF4 Vue 2 → Vue 3 Migration Reference

Exact breaking-change surface in `openy_af4_vue_app/`, with file + line
anchors — the source of truth for the rewrite phases (analog of the canvas
queue's `STYLE-REFERENCE.md`). Measured on the fork `6.x` branch. Update
after each phase ships.

> Canonical breaking-change list: [Vue 3 Migration Guide](https://v3-migration.vuejs.org/).
> This doc records only what **actually appears in AF4**.

---

## Dependency deltas

| Package | Current (Vue 2) | Target (Vue 3) | Notes |
|---|---|---|---|
| `vue` | `^2.6.14` | `^3.4` | core |
| `vue-router` | `^3.5.3` | `^4` | `createRouter` / `createWebHistory` |
| `vue-template-compiler` | `^2.6.14` | **removed** | replaced by `@vue/compiler-sfc` (pulled by build tool) |
| `bootstrap-vue` | `^2.22.0` | **no Vue 3 build** | W1 decision: replacement |
| `bootstrap` | `^4.6.1` | `^4` keep / `^5` if replacement needs it | tied to W1-P1 |
| `@fortawesome/vue-fontawesome` | `^2.0.6` | `^3` | Vue 3 build |
| `@iconify/vue2` | `^2.1.0` | `@iconify/vue` `^4` | package rename |
| `@vue/cli-service` | `^4.5.13` | `^5` **or** Vite | W1-P0 decision |
| `eslint-plugin-vue` | `^5` | `^9` | Vue 3 rules |
| `@vue/eslint-config-prettier` / `prettier` | `^5.1.0` / `^1.18.2` | bump | lint pass W5-P5 |

---

## 1. Global app bootstrap — `src/main.js`

**Vue 2 (current):**

```js
import Vue from 'vue'
Vue.config.devtools = process.env.NODE_ENV === 'development'
import BootstrapVue from 'bootstrap-vue'
Vue.component('font-awesome-icon', FontAwesomeIcon)
Vue.config.productionTip = false
Vue.use(BootstrapVue)
Vue.filter('capitalize', …)
Vue.filter('t', …)
Vue.filter('formatPlural', …)
Vue.mixin({ computed: { isIosMobile }, methods: { trackEvent, t, formatPlural, getCookie } })
new Vue({ router, components: { 'activity-finder': App } }).$mount('#activity-finder')
```

**Vue 3 (target shape):**

```js
import { createApp } from 'vue'
const app = createApp(App)
app.config.globalProperties.$xyz = …   // for the mixin methods, or a composable
app.component('font-awesome-icon', FontAwesomeIcon)
app.use(router)
// app.use(<bootstrap replacement>)     // per W1-P1
// filters → methods/computed/global props (see §3)
app.mount('#activity-finder')
```

**Watch-outs:**

- `Vue.config.productionTip` / `Vue.config.devtools` are **removed** in Vue 3
  (devtools auto-detect; no productionTip). Drop them.
- The current bootstrap registers a wrapper component `'activity-finder': App`
  and mounts `#activity-finder`. Vue 3 `createApp(App).mount('#activity-finder')`
  mounts `App` directly. **Confirm the Drupal template mounts on
  `#activity-finder` and the `App` root renders `#activity-finder-app`** —
  preserve both ids (W0-P0 records them).
- The GA `document.addEventListener('openy_activity_finder_event', …)` block is
  framework-agnostic — keep as-is.

## 2. Router — `src/router/index.js`

**Vue 2:** `Vue.use(VueRouter); new VueRouter({ mode: 'history', routes: [] })`

**Vue 3:** `createRouter({ history: createWebHistory(), routes: [] })`

`routes` is **empty** — the router is vestigial (AF4 drives screens via `step`
state in `App.vue`, not routes). W3-P1 confirms whether the router can be
dropped entirely or must stay for `history` side effects. **Do not assume —
verify against `App.vue` usage of `$router`/`$route` first.**

## 3. Template filters — REMOVED in Vue 3

`Vue.filter` and the `{{ value | filter }}` syntax are **gone**. AF4 has:

| Filter | Defined | Template usages (approx) | Vue 3 replacement |
|---|---|---|---|
| `t` | `main.js` + `Vue.mixin` method `t` | ~88 | call `t(...)` method (already exists on the mixin) |
| `formatPlural` | `main.js` + mixin method | ~12 | call `formatPlural(...)` method (already exists) |
| `capitalize` | `main.js` only | ~3 | small method/computed or inline |

**Key advantage:** `t` and `formatPlural` already exist as **mixin methods**,
so `{{ x | t }}` → `{{ t(x) }}` reuses the same implementation. `capitalize`
exists only as a filter — add a method. **i18n parity is mandatory**: keep the
`context: 'Activity Finder'` argument and `window.Drupal.t/formatPlural`
calls byte-identical (RULES → "i18n parity"). W5-P0 owns this conversion.

## 4. BootstrapVue — no Vue 3 build

Consumers (7 source files + `main.js`):

| File | What it uses (verify on read) |
|---|---|
| `components/modals/Modal.vue` | `b-modal` / `$bvModal` — core modal shell, depended on by every modal |
| `components/Fieldset.vue` | b-* form control |
| `components/Foldable.vue` | b-* collapse/toggle |
| `components/FoldableInput.vue` | b-* collapse/input |
| `components/filters/Ages.vue` | b-* form control |
| `components/steps/SelectAges.vue` | b-* form control |
| `components/ResultsBar.vue` | b-* (likely `b-dropdown`/`b-modal` trigger) |

Replacement options (decided in W1-P1, **not** prejudged here): BootstrapVue
Next (`bootstrap-vue-next`, Vue 3, still maturing) · plain Bootstrap 5 markup +
small custom components · hand-rolled replacements for the handful of widgets
actually used. **Decision criterion:** smallest surface that preserves the
existing markup/classes so the W0 visual baseline still matches.

## 5. Slots

`v-slot` (used in 17 components incl. `App.vue`, `ResultsBar.vue`, the steps)
is **compatible** in Vue 3. Only legacy `slot="x"` / `slot-scope="x"`
attribute syntax needs rewriting — W5-P3 greps for those and fixes any found.

## 6. Not present in AF4 (smaller scope than AF3/CF)

Confirmed absent on audit — **do not write phases for these**:

- No global `EventBus` / `new Vue()` event bus, no `$on` / `$off` /
  `$root.$emit`. (AF3 and Camp Finder use these; AF4 does not.)
- No `.sync` modifiers.
- No `$listeners` / `$children` / `$attrs` fall-through usage.

## 7. Critical Migration Gaps & Best Practices

Based on research and environmental analysis, three critical architectural gaps must be addressed:

### A. Vue Global Version Collision (Vue 2 vs Vue 3 on the same Drupal site)
- **Problem:** Other modules on the site (AF3 and Camp Finder) still run Vue 2 and depend on the global `window.Vue` (Vue 2) provided by `openy_system/vue`. If AF4 is compiled with `vue` as an external dependency (standard Webpack/Vite library mode), it will attempt to use the global `window.Vue` and crash because it is Vue 2.
- **Solution:** We must either bundle Vue 3 and Vue Router 4 directly inside the `activity_finder_4.umd.min.js` bundle (self-contained micro-frontend), or register a new Drupal library (e.g. `openy_system/vue3`) that exposes a `window.Vue3` global, and configure our build tool's Rollup/Webpack externals to map `vue` imports to `Vue3`. Bundling is the recommended best practice to prevent global dependency version conflicts.

### B. Bootstrap 4 Class/Styling Parity
- **Problem:** Modern Vue 3 Bootstrap libraries like `bootstrap-vue-next` target Bootstrap 5. However, the parent Drupal theme and pages operate on Bootstrap 4 (`bootstrap ^4.6.1` is in `package.json`). Loading Bootstrap 5 CSS will break the main site styles.
- **Solution:** The BootstrapVue replacement strategy must use custom wrapper components or template markup that compiles against Bootstrap 4 utility classes (e.g., using `mr-2` instead of `me-2`, preserving BS4 grids/flexbox structure).

### C. Migration Build (`@vue/compat`) for De-risking
- **Problem:** Swapping Vue 2 for Vue 3 directly might lead to hidden runtime exceptions due to deprecated APIs (e.g., event emitter behavior, slot scope syntax).
- **Solution:** Configure `@vue/compat` (the Vue 3 Migration Build) in development mode for waves W3/W4. This logs deprecation warnings in the browser console, allowing incremental refactoring of individual component deprecations before switching to pure Vue 3 for the production build.

## 8. Drupal consumer contract (must survive)

| Contract point | Value | Recorded in |
|---|---|---|
| Library id | `activity_finder_4` | `openy_activity_finder.libraries.yml:62` |
| JS artifact | `openy_af4_vue_app/dist/activity_finder_4.umd.min.js` | `libraries.yml:65` |
| CSS artifact | `openy_af4_vue_app/dist/activity_finder_4.css` | `libraries.yml:68` |
| UMD global / build name | `activity_finder_4` | `package.json` build script `--name activity_finder_4` |
| Mount element | `#activity-finder` | `main.js` `$mount` |
| Root render id | `#activity-finder-app` | `App.vue` template |
| Runtime deps | `window.Drupal.t`, `window.Drupal.formatPlural`, `window.drupalSettings.path.baseUrl` | `main.js`, `client/index.js` |

W0-P0 verifies and freezes this table; W7-P0 re-checks the produced build
against it.
