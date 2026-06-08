# P5 — QA: Responsive parity (1920 / 1024 / 468)

## Goal

Confirm every screen holds parity across the three QA viewports, catching
breakpoint-only regressions that single-viewport phases (P0–P4) miss.

## Steps

1. For each screen QA'd in P0–P4, capture at **1920**, **1024**, **468** px and
   diff against the matching baseline triplet.
2. Focus on responsive behaviours: wizard bar collapse, filters moving into the
   mobile `Filters` modal, results grid reflow, modal full-screen on mobile,
   touch targets.
3. Fill the `verified` column only when all three viewports match for a row.
   Record any breakpoint-specific delta in `Note`.

## Tests

```sh
capture-website "<af4 url + screen>" --output <screen>-1920.png --full-page --width 1920
capture-website "<af4 url + screen>" --output <screen>-1024.png --full-page --width 1024
capture-website "<af4 url + screen>" --output <screen>-468.png  --full-page --width 468
```

## Validation

Owner + Ira approve. Every Ira QA row is `verified` at all three viewports, or
carries an owner-accepted note. This is the W6 exit gate → W7 may start.

## Out of scope

- Code fixes — regressions route back to the owning W4/W5 phase.

## Result

(to be filled when phase ships)

Three-viewport parity confirmed across all screens; INDEX progress table fully
`verified`.
