# Wave W8 — Retro & upstream (after all done)

The closing wave. After the migration ships (W7), feed what *actually*
happened back into the shared anatomy so the next migration starts ahead of
this one. This is the **anatomy-upstream-PR obligation** (`~/.claude/CLAUDE.md`)
made an explicit, gated wave instead of an afterthought.

**Depends on W7 (shipped + PR #1 merged).** Final wave — closes ITCR-1273.

## Why this wave exists

The migration-queue anatomy this queue follows lives upstream in
`template_for_agents`:
[`process-knowledge-base/MIGRATION-QUEUE-ANATOMY.md`](https://github.com/ITCare-Company/template_for_agents/blob/main/process-knowledge-base/MIGRATION-QUEUE-ANATOMY.md)
(seeded by [PR #80](https://github.com/ITCare-Company/template_for_agents/pull/80),
merged). A queue that *uses* an anatomy but never returns its lessons lets the
pattern drift — the next team re-derives the same gotchas. W8 returns them.

## Phases

| Phase | Goal | Status |
|---|---|---|
| P0 | Harvest lessons learned — real deviations from plan, gotchas, decisions that surprised us. | pending |
| P1 | Enrich the upstream anatomy doc (PR to `template_for_agents`) with any new pattern / best practice. | pending |
| P2 | Subtree-import this finished queue as a PKB entry; register it. | pending |
| P3 | Back-reference + close — cite the upstream anatomy as the methodology source; close ITCR-1273. | pending |

## Done when

- A retro doc exists with the real lessons.
- The upstream `MIGRATION-QUEUE-ANATOMY.md` carries any new pattern (or P1
  explicitly records "nothing new — anatomy held").
- The finished `queue/` is a registered PKB entry.
- This queue's README/INDEX point at the upstream anatomy; ITCR-1273 closed.

## Decisions

See [`DECISIONS.md`](DECISIONS.md).

## Out of scope

- Any AF4 code change — migration is done in W7. W8 is documentation +
  knowledge transfer only.

## Onboarding (UA — Lera)

W8 — фінальна хвиля «після всього». Коли міграція вже в проді (W7), збираємо
реальні уроки (що пішло не за планом, які підводні камені) і віддаємо їх
назад у спільну анатомію в `template_for_agents`. Так наступна міграція
почнеться з нашого досвіду, а не з нуля. Код тут не чіпаємо — лише знання.
