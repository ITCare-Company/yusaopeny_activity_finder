# Wave W0 — Baseline & contract

Freeze the **"before"** state of AF4 so every later wave has a fixed
reference to diff against. Nothing migrates until this wave is signed off.

**Blocks every other wave.** A migration without a frozen baseline cannot be
proven correct.

## Phases

| Phase | Goal | Status |
|---|---|---|
| P0 | Inventory the exact Drupal consumer contract (library, global, mount, deps). | pending |
| P1 | Capture golden screenshots of every screen/step/modal/filter — seeds Ira's QA checklist in `INDEX.md`. | pending |
| P2 | Audit the precise Vue 3 breaking-change surface; confirm the counts in `MIGRATION-REFERENCE.md`. | pending |

## Done when

- The Drupal contract table in [`../MIGRATION-REFERENCE.md`](../MIGRATION-REFERENCE.md) §7 is verified and signed off.
- Baseline PNGs exist for every component row in `INDEX.md` → "Progress — Ira QA view".
- The breaking-change audit confirms (or corrects) every count this queue relies on.

## Decisions

See [`DECISIONS.md`](DECISIONS.md).

## Out of scope

- Any change to AF4 source — this wave is read-only + screenshots.
- AF3 / Camp Finder.

## Onboarding (UA — Lera/Ira)

W0 — це «фотографія до». Записуємо, як Drupal підключає AF4 (щоб нічого не
зламати на стику), і робимо знімки кожного екрана. Ці знімки — еталон, з яким
Іра потім порівнює Vue 3 версію у W6.
