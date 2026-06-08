# Wave W2 — Toolchain migration (still Vue 2)

Migrate the build tool **before** swapping the framework, so a green build
proves the toolchain in isolation. The app stays on Vue 2 through this wave.

**Blocks W3** (don't mix a build-tool change and a framework change in one
diff — if the build breaks, the cause must be unambiguous).

Single-phase wave. Decision input: W1-P0 (`DECISIONS.md` D1).

## Phases

| Phase | Goal | Status |
|---|---|---|
| P0 | Move to the chosen build tool, still Vue 2, with an identical `dist/` contract. | pending |

## Done when

`npm run build` produces a `dist/activity_finder_4.umd.min.js` + `.css` that
loads in Drupal identically to the pre-migration artifact (W0-P0 contract).

## Out of scope

- Any Vue 3 change (W3+).
- Replacing BootstrapVue (W4).

## Onboarding (UA — Lera)

W2 — міняємо лише систему збірки, застосунок ще на Vue 2. Так ми переконуємось,
що збірка працює, до того як чіпати саме Vue. Результат `dist/` має лишитись
сумісним з Drupal.
