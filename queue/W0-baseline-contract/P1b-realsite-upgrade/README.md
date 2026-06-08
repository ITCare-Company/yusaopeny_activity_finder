# P1b — Real-site upgrade-path baseline

## Goal

Capture a **second** "before" baseline on a **real Open Y site** (existing
content, real config, real theme), distinct from the W0-P1 sandbox baseline.
This is the upgrade-path reference: W7 verifies the migrated AF4 drops into an
existing install without breaking its rendered screens.

> **Two-stage baseline (why).** W0-P1 (sandbox) is the **clean, reproducible**
> reference — controlled demo content, Mock/DB backend, deterministic screens —
> the primary source for golden screenshots and Ira's parity table. P1b is the
> **upgrade-path** reference — a real site proves the new build replaces the old
> one on a live install (real data volume, real theme CSS, real Solr) without
> regression. Sandbox first (stable), real site second (realistic).

## Files

Read-only against a running real-site AF4. Output: PNGs under
`baseline-realsite/` in this phase folder + a note recording the site, its AF4
`libraries.yml` version, and content scale.

## Steps

1. **Nominate a real Open Y site** running AF4 (Vue 2, current `dist/`). Record
   URL + AF4 version in `INDEX.md` → "Reference environments" (the
   "real-site / upgrade" row).
2. Capture the same screen set as W0-P1 (entry, steps, results, modals,
   filters) at 1920 / 1024 / 468, into `baseline-realsite/`.
3. Note real-site-only differences from the sandbox (theme overrides, content
   volume, Solr facet counts) so W7 expects them and does not flag them as
   migration regressions.

## Tests

No automated test. Exit: the real-site screen set is captured at all three
viewports, and deltas vs the sandbox baseline are documented (not surprises).

## Validation

Owner approves. Real-site baseline captured; site + AF4 version recorded;
sandbox-vs-real deltas documented. W7 uses this set for the live upgrade smoke.

## Out of scope

- Migrating the real site (that is the W7 upgrade verification, not W0).
- Changing the real site in any way — read-only capture.

## Result

(to be filled when phase ships)

Real-site upgrade baseline captured; deltas vs sandbox documented; W7 upgrade
reference ready.
