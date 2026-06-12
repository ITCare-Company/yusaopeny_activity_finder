# P6 — BUG: URL state edge cases after History API swap (PR #11)

**Found during:** post-merge review of PR #11.
**Owner:** Lera.
**Status:** OPEN.
**QA:** covered by `W6-qa-visual-functional/P6-url-state/` scenarios once fixed.

Two related edge-case bugs in `openy_af4_vue_app/src/App.vue`, both fallout
of the vue-router → native History API swap. Main flows work; these bite on
deep links and back/forward.

## Bug 1 — no same-URL guard before `pushState`

`updateUrl()` (App.vue:857–880) always calls
`history.pushState({}, '', newUrl)` — even when `newUrl` equals the current
URL. Unlike the old `$router.push()`, which threw `NavigationDuplicated`
(caught → no entry), `pushState` **always** adds a history entry, identical
URL or not.

### Repro

1. Open a deep link, e.g. `?step=selectAges&selectedAges=6`.
2. `created()` runs `getDataFromUrl()` (App.vue:716) → state mutates →
   `updateUrlString` watcher (App.vue:669–670) fires on the pre-mount flush →
   `updateUrl()` → `pushState` of the same (or equivalent) URL.
3. One duplicate history entry exists before the user touches anything.
4. Press Back once → `popstate` lands on the duplicate entry, page does not
   leave. Second Back required.

Breaks the W6-P6 scenario **"Back to page start — no stuck loop"** for any
deep-linked entry. Clean loads (no query params) are unaffected — defaults
don't change `updateUrlString`, the watcher never fires.

## Bug 2 — `_restoringFromHistory` flag can go stale

The flag is set in the `popstate` handler (App.vue:726) but cleared **only**
inside `updateUrl()` (App.vue:872–873). The clear therefore depends on the
restore actually changing `updateUrlString`. When it doesn't — e.g. popstate
between the duplicate/equivalent entries from Bug 1, or two entries differing
only in a param outside `defaults` — the watcher never fires, the flag stays
`true`, and the **next genuine user interaction is swallowed**: state changes,
`updateUrl()` consumes the stale flag and returns, URL silently not updated.

Compounds with Bug 1: deep link → duplicate entry → Back → no-op restore →
stale flag → next filter change loses URL sync.

Minor (fold into the same fix): the flag is initialized in `mounted()`
(App.vue:724), but `updateUrl()` can run on the pre-mount flush where it reads
`undefined`. Works today because `undefined` is falsy — fragile, not correct.

## Fix direction

1. **Same-URL guard** in `updateUrl()`: after building `newUrl`, return
   without pushing when it equals
   `window.location.pathname + window.location.search`.
2. **Deterministic flag consumption**: clear `_restoringFromHistory` in the
   `popstate` handler itself via `this.$nextTick(...)` after
   `getDataFromUrl()` completes, instead of relying on `updateUrl()` being
   reached. Keep the check in `updateUrl()`.
3. **Initialize the flag before first use**: set `_restoringFromHistory =
   false` in `created()` (before `getDataFromUrl()`), not in `mounted()`.

`dist/` rebuild policy per queue FOLLOWUPS / W7-P0.

## Verification

All 7 scenarios in `W6-qa-visual-functional/P6-url-state/README.md`, plus:

| Scenario | Expected |
|---|---|
| Deep link `?step=selectAges&selectedAges=6`, press Back **once** | Browser leaves the AF page — no second press needed |
| Deep link → Back → re-enter via Forward → change a filter | URL updates in the address bar (flag not stale) |

## Refs

- PR #11 (`fix/af4-url-query-state-history-api`) — the swap this follows up.
- ITCR-1273.
- Original bug write-up `P4-bug-url-query-state/README.md` exists only on the
  unmerged `bug/af4-url-query-state-reload` branch — never landed on 7.x;
  this phase supersedes it for the remaining edge cases.

## Result

(to be filled when fixed)
