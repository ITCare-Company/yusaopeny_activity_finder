# Wave W6 — QA visual + functional (Ira)

Walk every AF4 screen on the Vue 3 build and diff against the W0-P1 baseline,
at the three viewports. **This is the wave Lera's extension was built for** —
it is driven by the "Progress — Ira QA view" table in
[`../INDEX.md`](../INDEX.md): one row per component, `baseline` ✓ (W0) and
`verified` ✓ (here).

**Depends on W4 + W5 (code migrated). Blocks W7.**

## Phases

| Phase | Scope | Owner | Status |
|---|---|---|---|
| Psetup | Deploy sandbox — accessible test URL for Ira | @svicervlad | pending |
| P0 | Entry + SelectPath (App, SelectPath, WizardBar, Step) | @ira | pending |
| P1 | Wizard steps (7 step screens) | @ira | pending |
| P2 | Results (Results, ResultsList, ResultsBar, AvailableSpots, NoResults, Loading) | @ira | pending |
| P3 | Modals (6 modals) | @ira | pending |
| P4 | Filters (16 filter components) | @ira | pending |
| P5 | Responsive parity (1920 / 1024 / 468 across all screens) | @ira | pending |
| P6 | URL query state — deep link, reload, back/forward (PR #11 fix) | @ira | pending |
| P7 | MockBackend filtering — age, categories, locations, daystimes (PR #12) | @shuklina | pending |

**P0–P5 blocked until Psetup is done.**

## How a phase works (Ira loop)

For each component in the phase's group:
1. Open the screen on the Vue 3 build at the URL from Psetup.
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

## Test site

**URL:** set by @svicervlad — see [`Psetup-sandbox-deploy/README.md`](Psetup-sandbox-deploy/README.md)
**Backend:** Mock (no Solr needed — 31 static sessions)
**W0 baseline screenshots:** `queue/W0-baseline-contract/P1-behavioral-baseline/screenshots/`
**W6 reference screenshots (Vue 3 smoke):** `queue/W6-qa-visual-functional/screenshots/`

## Known intentional differences vs W0 baseline (do NOT flag as regressions)

See DECISIONS.md for full list. Short summary:
- Modal close button: `×` char instead of Iconify icon (W6-D1)
- Disabled age tooltip: browser `title` attr instead of styled BV tooltip (W6-D2)

## High-risk areas from W4/W5 migration (extra attention needed)

These areas had bugs during migration — verify carefully:
- **Modal open + close** (P3): X button, ESC, backdrop click — all three must work (W4-D1 teleport issue was fixed)
- **Modal body content** (P3): ActivityDetails must show session name, date, location, price (Vue 3 slot fix)
- **Filter v-model** (P4): selecting a filter must update results (v-model Vue 3 migration W5-P2)
- **Results count** (P2): pluralized count e.g. "31 results" — filter pipe → method call (W5-P0)
- **Foldable/Fieldset collapse** (P4): click to expand/collapse must work (W4-P1/P2 collapse rewrite)

## Onboarding (UA — Ira/Lera)

W6 — Іра проходить кожен екран на Vue 3 і порівнює зі знімками з W0. Таблиця —
в [`../INDEX.md`](../INDEX.md) («Progress — Ira QA view»). Збігається — стави
`✓` у колонці `verified`. Не збігається — пиши що саме в `Note`, і це
повертається у відповідну фазу W4/W5 на виправлення.
