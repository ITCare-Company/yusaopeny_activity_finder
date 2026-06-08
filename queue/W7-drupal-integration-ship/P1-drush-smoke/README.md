# P1 — drush smoke on a live Open Y site

## Goal

Confirm the Vue 3 AF4 bundle loads and works end-to-end on a real Drupal Open Y
site, not just the dev harness.

## Files

- No source change. Verification on a site that has `openy_activity_finder`
  enabled (DDEV-based, per CLAUDE.md drush/composer run inside ddev).

## Steps

1. On a DDEV Open Y site, point the `openy_activity_finder` module at this
   fork/branch (composer path repo or checkout), with the rebuilt `dist/`.
2. Clear caches:
   ```sh
   ddev drush cr
   ```
3. Load the AF4 page. Verify:
   - the `activity_finder_4` library loads (no 404 on the UMD/CSS);
   - AF4 mounts on `#activity-finder`;
   - a full wizard → results → modal flow works against real `af/get-data` /
     `session-data` / `more-info` endpoints;
   - `Drupal.t` / `formatPlural` translations render;
   - no console errors.
4. Capture a short screen recording / screenshots of the working flow.

## Tests

Manual end-to-end on the live site + `ddev drush cr` clean.

## Validation

Owner approves. AF4 works end-to-end on a real site against real APIs; no
console errors; i18n intact.

## Out of scope

- PR creation (P2).
- Performance tuning.

## Result

(to be filled when phase ships)

Live-site smoke passed; evidence attached.
