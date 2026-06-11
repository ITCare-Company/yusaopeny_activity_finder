# P3 — CSP review for the esm-bundler runtime compiler

## Goal

Verify that shipping `vue/dist/vue.esm-bundler.js` (the full build with the
runtime template compiler) does not break Activity Finder on Open Y sites that
enforce a strict Content-Security-Policy, and document the outcome for the W7
contract.

## Why

W5 aliased `vue` to `vue.esm-bundler.js` so the root component can compile the
**DOM template** Drupal renders from twig props (Vite library mode defaults to
runtime-only and would not compile it). The runtime compiler generates render
functions at runtime via `new Function(...)`, which a strict CSP without
`script-src 'unsafe-eval'` blocks — the app would white-screen on those sites
even though the build is green locally.

AF is widely deployed across YMCA Open Y sites with varying CSP posture, so
this is a real consumer-contract risk that only surfaces on the live site, not
in `npm run build`. It must be checked before ship, not after.

## Files

- `openy_af4_vue_app/vite.config.js` — the `vue` → `vue.esm-bundler.js` alias.
- `queue/W7-drupal-integration-ship/` `DECISIONS.md` — record the CSP outcome
  and, if needed, the mitigation chosen.

## Steps

1. Confirm whether any target Open Y site sets a CSP that omits
   `'unsafe-eval'` (check the `csp` / `seckit` module config, or response
   headers on a live site).
2. If CSP is permissive everywhere AF4 ships — record that fact + the
   `new Function` dependency in `DECISIONS.md`; no code change.
3. If any target enforces strict CSP — evaluate the runtime-only mitigation:
   move twig-rendered props into a **mount-element data attribute / JSON
   script** that the root component reads as data instead of compiling a DOM
   template, then drop the esm-bundler alias back to runtime-only. Scope that
   as its own phase if chosen.
4. Re-run W7-P1 drush smoke against a strict-CSP site (or a local site with
   `seckit` configured) and confirm AF4 mounts.

## Tests

```sh
cd openy_af4_vue_app && npm run build
```

Live: load the AF4 page on a strict-CSP Open Y site; console must show no CSP
violation and no "runtime compilation is not supported" / `unsafe-eval`
error.

## Validation

Owner approves. CSP posture documented in `DECISIONS.md`; AF4 mounts on the
target sites; if strict CSP exists, a mitigation path is recorded (not
silently shipped broken).

## Out of scope

- Implementing the runtime-only refactor — this phase decides whether it is
  needed and records the decision; the refactor (if any) is a separate phase.

## Result

(to be filled when phase ships)
