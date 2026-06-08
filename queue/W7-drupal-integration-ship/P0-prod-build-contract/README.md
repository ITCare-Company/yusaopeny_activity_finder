# P0 — Production build + contract verify

## Goal

Build AF4 for production on Vue 3 and verify the output satisfies the W0-P0
Drupal consumer contract, then commit the regenerated `dist/`.

## Files

- `openy_af4_vue_app/dist/activity_finder_4.umd.min.js` (regenerated)
- `openy_af4_vue_app/dist/activity_finder_4.css` (regenerated)
- `openy_activity_finder.libraries.yml` — confirm unchanged (the paths still
  resolve).

## Steps

1. Clean build:
   ```sh
   cd openy_af4_vue_app && npm ci && npm run build
   ```
2. Verify against the W0-P0 contract table:
   - both `dist/` filenames produced;
   - UMD exposes the `activity_finder_4` global;
   - CSS extracted to the sibling `.css`;
   - `libraries.yml` paths (`:65`, `:68`) still match.
3. Commit the regenerated `dist/` as the build artifact (per RULES: only a
   clean-build `dist/` may be committed — never hand-edited).
4. Note bundle-size delta vs the Vue 2 build (BootstrapVue removed should
   shrink it).

## Tests

```sh
cd openy_af4_vue_app && npm ci && npm run build
# inspect dist/ filenames + grep the UMD for the global name
```

## Validation

Owner approves. Build is a clean reproducible output; contract table all green;
`dist/` committed from a real build.

## Out of scope

- Live-site smoke (P1); PR (P2).

## Result

(to be filled when phase ships)

Production `dist/` rebuilt + committed; contract verified; size delta recorded.
