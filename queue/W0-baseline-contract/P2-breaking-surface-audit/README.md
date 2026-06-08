# P2 — Breaking-change surface audit

## Goal

Confirm (or correct) the exact Vue 3 breaking-change surface in AF4 so the
W3–W5 phases plan against real counts, not estimates. Produces the final,
trusted version of `MIGRATION-REFERENCE.md`.

## Files

Read-only. Output: corrections committed into
[`../../MIGRATION-REFERENCE.md`](../../MIGRATION-REFERENCE.md) + an audit log
`findings.md` here.

## Steps

Re-run each audit on the current `6.x` source and reconcile against
`MIGRATION-REFERENCE.md`:

1. **Global API** — grep `src/main.js` for `Vue.use|Vue.component|Vue.config|Vue.mixin|Vue.filter|new Vue`. Confirm the list in §1.
2. **Filters** — count `| capitalize`, `| t`, `| formatPlural` template usages:
   ```sh
   grep -rhoE "\| ?(capitalize|t|formatPlural)\b" src | sort | uniq -c
   ```
   Confirm ~88 / ~12 / ~3 (≈103 total).
3. **BootstrapVue consumers** — `grep -rlE "bootstrap-vue|<b-|\$bvModal|v-b-" src`. Confirm the 7-file list (§4) and note, per file, exactly which `b-*` components / directives are used (drives W4 phase scoping).
4. **Router** — confirm `routes` is empty and check whether `$router` / `$route` are referenced anywhere (`grep -rn "\$route" src`). Feeds the W3-P1 "drop or keep router" decision.
5. **Slots** — `grep -rlE "v-slot|slot-scope|slot=" src`. Flag any legacy `slot=` / `slot-scope=` (none expected; `v-slot` is fine).
6. **Confirm absences** — re-verify no `EventBus` / `$on` / `.sync` / `$listeners` / `$children` in AF4.
7. Update `MIGRATION-REFERENCE.md` with any delta and note the audit date.

## Tests

No automated test. Exit: every count in `MIGRATION-REFERENCE.md` carries a
reproducible grep and matches the audit.

## Validation

Owner reviews `findings.md`. Confirm the audit is reproducible (each claim has
a command) and the reference doc is now trusted for W3–W5 planning.

## Out of scope

- Any code change. Read-only audit.
- Deciding the replacement strategy (that is W1).

## Result

(to be filled when phase ships)

`findings.md` shipped; `MIGRATION-REFERENCE.md` reconciled with measured
counts; any surprise (extra BootstrapVue widget, unexpected `$route` usage)
logged in W0 `DECISIONS.md`.
