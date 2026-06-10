# How this queue started

Saved verbatim so anyone joining (Lera, Ira, future devs) understands the
origin, intent, and the two source patterns this queue fuses.

## The kickoff request (UA, verbatim)

> Давай створимо дуууууже детальну queue згідно анатомії
> https://github.com/ITCare-Company/ymca_ws_canvas_demo/tree/feat/standard-pass-w1/queue
>
> В цю анатомію (в inventory.tsv та INDEX.md потрібно інтегрувати розширення,
> яке придумала Lera для зручності роботи з Ira
> https://github.com/ITCare-Company/ymca_ws_canvas_demo/blob/feat/standard-pass-w1/docroot/themes/custom/y_canvas/docs/wip/STANDARD-PASS-PROGRESS.md)
> для завдання https://jet-dev.atlassian.net/browse/ITCR-1273
>
> Робочий fork для цього https://github.com/ITCare-Company/yusaopeny_activity_finder
>
> Збережи цей prompt в queue, щоб було зрозуміло, як стартанула черга, для
> onboarding Lera

## What that means in practice

The request fuses **two** existing patterns into one new queue:

1. **The anatomy** — the wave/phase queue structure from
   [`ymca_ws_canvas_demo/queue`](https://github.com/ITCare-Company/ymca_ws_canvas_demo/tree/feat/standard-pass-w1/queue):
   `README.md` + `INDEX.md` + `RULES.md` + `inventory.tsv`, then one directory
   per **wave**, one subdirectory per **phase** with a
   `Goal / Files / Steps / Tests / Validation / Out of scope / Result`
   `README.md`, and a `DECISIONS.md` per multi-phase wave.

2. **Lera's extension** — the progress-tracking format she invented in
   [`STANDARD-PASS-PROGRESS.md`](https://github.com/ITCare-Company/ymca_ws_canvas_demo/blob/feat/standard-pass-w1/docroot/themes/custom/y_canvas/docs/wip/STANDARD-PASS-PROGRESS.md)
   to make QA with **Ira** easy. Its shape:
   - a **Reference environments** block at the top (the live URLs to compare
     against);
   - a per-row **live preview URL / path** so the reviewer opens the exact
     screen in one click;
   - two **checkmark columns** proving the two states match (in her doc:
     `small_y` vs `standard`);
   - a free-text **Note** column for the specific visual delta / gotcha.

   That extension is **folded into this queue's [`INDEX.md`](INDEX.md)**
   (the "Progress — Ira QA view" table) and **[`inventory.tsv`](inventory.tsv)**
   (extra columns `preview_path`, `baseline`, `verified`, `note`). For a Vue 2
   → Vue 3 migration the two checkmark columns become **`baseline`** (captured
   in W0, the "before") and **`verified`** (W6 confirms the "after" matches) —
   the same "prove the two states are identical" idea, applied to before/after
   instead of small_y/standard.

3. **The work** — [ITCR-1273](https://jet-dev.atlassian.net/browse/ITCR-1273):
   upgrade AF4 (`openy_af4_vue_app/`) from Vue 2 to Vue 3.

4. **The home** — the fork
   [`yusaopeny_activity_finder`](https://github.com/ITCare-Company/yusaopeny_activity_finder),
   branch `6.x`. This queue scaffold landed on `feat/itcr-1273-vue3-queue`.

## Onboarding pointers (UA — для Lera)

- Почни з [`README.md`](README.md) — там вся карта хвиль і чому така
  послідовність.
- [`INDEX.md`](INDEX.md) — плаский список усіх фаз + **таблиця прогресу для
  Іри** (твоє розширення). Сюди дивишся, щоб бачити стан.
- [`inventory.tsv`](inventory.tsv) — те саме, але машинно-читане (одна фаза =
  один рядок), з твоїми колонками для QA.
- [`MIGRATION-REFERENCE.md`](MIGRATION-REFERENCE.md) — шпаргалка breaking
  changes Vue 2 → 3 із точними файлами/рядками в AF4 (аналог
  `STYLE-REFERENCE.md` з canvas-черги).
- [`RULES.md`](RULES.md) — гейти діалогу, формат комітів, гейт апруву на
  кожну фазу.
- [`FOLLOWUPS.md`](FOLLOWUPS.md) — наступні задачі з рев'ю (status sync,
  `.DS_Store`, dist policy, anatomy completeness).
- **HOW — методологія** (як вести чергу, не _що_ робити):
  [`_shared/behaviors/`](https://github.com/ITCare-Company/template_for_agents/tree/main/process-knowledge-base/_shared/behaviors)
  у `template_for_agents` — 20 патернів (PR-as-question-channel, ESC-steering,
  watchdog для нічних ранів, atomic-commits, phase-vs-wave vocabulary). Почни з
  `02-core-philosophy`, `03-start-and-resume`, `06-pr-as-question-channel`.

Робочий цикл фази: читаєш `README.md` фази → пишеш `## Plan` у чат → чекаєш
«вйо» → код → `Verify` (білд/лінт/скрін) → чекаєш «ok» → push у гілку хвилі.
