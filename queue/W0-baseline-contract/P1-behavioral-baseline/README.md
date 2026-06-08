# P1 — Behavioral baseline (sandbox golden screenshots)

## Goal

Capture the **"before"** render of every AF4 screen on the current Vue 2
build, at the three QA viewports, so W6 can diff Vue 3 against a fixed
reference. These screenshots are the source data for Ira's QA checklist in
`INDEX.md` → "Progress — Ira QA view".

> **Stage 1 of a two-stage baseline.** This phase is the **sandbox** baseline —
> a clean, reproducible site with controlled demo content (W0b-P4) and a
> deterministic backend (Mock or DB from W0b — **no Solr needed**). It is the
> primary golden-screenshot source. The **real-site** upgrade baseline is
> [`P1b-realsite-upgrade`](../P1b-realsite-upgrade/README.md) — captured on a
> live install for the W7 upgrade-path check.
>
> **Depends on W0b:** AF4 must render real screens, which needs a runnable
> backend + seeded content. Use the Mock backend to capture without standing up
> Solr.

## Files

Read-only against a running AF4. Output: PNGs under `baseline/` in this phase
folder, named `<component-or-screen>-<viewport>.png`.

## Steps

1. **Stand up the sandbox AF4 env.** A clean Open Y site with demo content
   (W0b-P4) and the AF4 block backend set to **Mock** (or DB) — no Solr
   required. (A `npm run dev` harness with stub `drupalSettings` + `Drupal.t`
   works for pure-frontend screens, but the Mock backend gives full data-driven
   screens.) Record the URL in `INDEX.md` → "Reference environments" (sandbox
   row).
2. Walk the wizard end to end and exercise every screen in the Ira QA table:
   entry/SelectPath, each wizard step, results, every modal, every filter.
3. Capture each at **1920**, **1024**, **468** px with `capture-website`:
   ```sh
   capture-website "<af4 url>" --output baseline/<screen>-1920.png --full-page --width 1920
   capture-website "<af4 url>" --output baseline/<screen>-1024.png --full-page --width 1024
   capture-website "<af4 url>" --output baseline/<screen>-468.png  --full-page --width 468
   ```
   For modal/step states that need interaction, script the open via the
   harness or capture manually; note the trigger in the filename / a
   `baseline/INDEX.md`.
4. Mark `baseline = ✓` for each captured component in `INDEX.md` and mirror to
   `inventory.tsv` (the W6 phase rows).

## Tests

No automated test. Exit: every row in the Ira QA table has a baseline PNG at
all three viewports (or an explicit "N/A — not reachable" note).

## Validation

Owner + Ira review the baseline set for completeness. Confirm no screen is
missing and the captures are at the agreed viewports.

## Out of scope

- Any source change.
- Pixel-diff tooling setup (that is W6) — this phase only produces the
  reference images.

## Result

(to be filled when phase ships)

Baseline PNG set shipped under `baseline/`. Ira QA table `baseline` column
filled. Live AF4 URL recorded in `INDEX.md`.
