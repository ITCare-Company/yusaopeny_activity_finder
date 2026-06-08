# Wave W6 — QA visual + functional (Ira)

Walk every AF4 screen on the Vue 3 build and diff against the W0-P1 baseline,
at the three viewports. **This is the wave Lera's extension was built for** —
it is driven by the "Progress — Ira QA view" table in
[`../INDEX.md`](../INDEX.md): one row per component, `baseline` ✓ (W0) and
`verified` ✓ (here).

**Depends on W4 + W5 (code migrated). Blocks W7.**

## Phases

| Phase | Scope | Status |
|---|---|---|
| P0 | Entry + SelectPath (App, SelectPath, WizardBar, Step) | pending |
| P1 | Wizard steps (7 step screens) | pending |
| P2 | Results (Results, ResultsList, ResultsBar, AvailableSpots, NoResults, Loading) | pending |
| P3 | Modals (6 modals) | pending |
| P4 | Filters (16 filter components) | pending |
| P5 | Responsive parity (1920 / 1024 / 468 across all screens) | pending |

## How a phase works (Ira loop)

For each component in the phase's group:
1. Open the screen on the Vue 3 build (live or harness).
2. Capture at the QA viewport(s); diff against `W0-baseline-contract/P1-behavioral-baseline/baseline/<screen>-<vp>.png`.
3. **Match** → set `verified = ✓` in the INDEX progress table + `inventory.tsv`.
   **Mismatch** → write the delta in the `Note` column, file a `fix(af4)`
   follow-up (back into the owning W4/W5 phase), keep `verified` blank until
   re-checked.

## Done when

Every component row in the Ira QA table has `verified = ✓` (or a documented,
owner-accepted intentional difference) at all three viewports.

## Decisions

See [`DECISIONS.md`](DECISIONS.md).

## Out of scope

- Code changes — W6 finds regressions; fixes land back in W4/W5 phases (with
  their own approval gate) and re-verify here.
- Production build / ship (W7).

## Onboarding (UA — Ira/Lera)

W6 — Іра проходить кожен екран на Vue 3 і порівнює зі знімками з W0. Таблиця —
в [`../INDEX.md`](../INDEX.md) («Progress — Ira QA view»). Збігається — стави
`✓` у колонці `verified`. Не збігається — пиши що саме в `Note`, і це
повертається у відповідну фазу W4/W5 на виправлення.
