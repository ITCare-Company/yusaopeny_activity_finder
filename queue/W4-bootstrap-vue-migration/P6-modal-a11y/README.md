# P6 — hand-rolled modal accessibility restore (follow-up to P0)

## Goal

Restore the accessibility behaviours the hand-rolled BS4 modal lost when
`<b-modal>` was replaced in P0 and `<teleport>` was dropped (W4-D1). The
current `Modal.vue` is a fixed-position div with `z-index` 2050/2040 and a
`document` keydown for ESC — visually correct, but it no longer provides the
focus management and semantics BootstrapVue gave for free.

## Why

`<b-modal>` shipped, and the migration must not silently regress, four a11y
behaviours none of which the hand-rolled version currently has:

- **Focus trap** — Tab/Shift+Tab must cycle inside the open modal, not escape
  to the page behind it.
- **Return focus** — on close, focus returns to the element that opened the
  modal (the result's "More info" trigger).
- **Dialog semantics** — `role="dialog"`, `aria-modal="true"`, and an
  `aria-labelledby` pointing at the modal title.
- **Body scroll-lock** — background must not scroll while the modal is open
  (BootstrapVue added `modal-open` to `<body>`).

Without these, keyboard and screen-reader users hit a broken `ActivityDetails`
/ `BookmarkedItems` / mobile `Filters` flow. This is a behaviour regression vs
Vue 2, so it is `fix(af4)`, not cosmetic — and it is the same shell every
modal depends on (W4-P0).

## Files

- `openy_af4_vue_app/src/components/modals/Modal.vue` — add focus trap, return
  focus, `role`/`aria-modal`/`aria-labelledby`, and body scroll-lock toggling
  on the `modelValue` watcher that already drives open/close.

## Steps

1. On open: record `document.activeElement`, move focus to the modal (or its
   first focusable / close button), add `modal-open` to `<body>`.
2. Trap Tab within the modal's focusable set; loop at both ends.
3. On close (X / ESC / backdrop): remove `modal-open`, restore focus to the
   recorded trigger.
4. Add `role="dialog"` + `aria-modal="true"` to the modal div and
   `aria-labelledby` referencing the title node; keep the existing
   `@click.self` backdrop + ESC handlers.
5. Verify nested usage (a modal opened from results) still traps/returns
   correctly.

## Tests

```sh
cd openy_af4_vue_app && npm run lint && npm run build
```

Manual: open `ActivityDetails` → Tab stays inside, Shift+Tab wraps, ESC
closes and focus returns to the "More info" trigger; background does not
scroll; screen-reader announces a dialog. Repeat for `BookmarkedItems` and
mobile `Filters`.

## Validation

Owner approves. Focus trapped + returned; `role="dialog"`/`aria-modal`
present; body scroll-locked while open; all three close paths intact; no
visual change vs the P0 modal.

## Out of scope

- Restyling the modal — a11y behaviour only, pixels unchanged.
- Re-introducing `<teleport>` (dropped per W4-D1) — trap works inline.

## Result

(to be filled when phase ships)
