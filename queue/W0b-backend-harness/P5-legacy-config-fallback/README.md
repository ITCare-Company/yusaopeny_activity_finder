# P5 — Legacy-config fallback (no silent backend flip)

> **Deferred / complex — likely Lera → Vlad handoff.** P0 ships a provisional
> `'solr'` default for blocks with no stored `backend_plugin`. That is wrong for
> any site whose **global** backend is not Solr — it would silently switch them.
> This phase replaces the hardcoded default with a real upgrade path. Land it
> before the plugin manager becomes the sole resolution path (i.e. before the
> legacy `settings.backend` service-id route is removed).

## Goal

When an AF4 block has no per-block `backend_plugin`, resolve its backend from
the **existing global `openy_activity_finder.settings.backend`** config, mapped
to the matching plugin id — so existing sites keep the backend they run today,
not a forced Solr.

## Files

- `src/Plugin/Block/ActivityFinder4Block.php` — `defaultConfiguration()` /
  render resolution: empty `backend_plugin` → derive from global
  `settings.backend` instead of literal `'solr'`.
- `src/Controller/ActivityFinderController.php` — same fallback on the controller path.
- New (optional): a `hook_update_N()` / config migration that stamps each
  existing block's `backend_plugin` from the current global `settings.backend`,
  so the fallback is a one-time upgrade, not a permanent runtime lookup.
- A service-id → plugin-id map (`openy_activity_finder.solr_backend` → `solr`;
  `openy_daxko2.openy_activity_finder_backend` → its plugin id **iff** that
  module is enabled and exposes a backend plugin — otherwise leave the legacy
  service-id path intact for it).

## Steps

1. Read the current global `settings.backend` service id.
2. Map known service ids to plugin ids; for an unmapped/legacy service id (e.g.
   a Daxko backend with no plugin yet), **keep resolving via the old
   service-id path** rather than guessing — no silent substitution
   (`feedback_no_fabricated_behavior`).
3. Use that mapping as the block's fallback when `backend_plugin` is empty.
4. Optionally add the `hook_update_N()` to persist per-block `backend_plugin`
   from the global setting (one-time), then the runtime fallback is belt-and-braces.
5. Only after this lands may the legacy `openy_activity_finder.solr_backend`
   alias / service-id route be considered for removal (coordinate with P0/P1).

## Tests

```sh
# A site whose global backend is NOT solr keeps its backend after upgrade.
ddev drush php:eval '\Drupal::configFactory()->getEditable("openy_activity_finder.settings")->set("backend","<non-solr-service-id>")->save();'
ddev drush cr
# Load the AF4 block — confirm it still resolves the non-solr backend, not Solr.
```

## Validation

Owner approves. No site is silently switched to Solr by the plugin migration;
unmapped/legacy backends keep working via the old path; if a `hook_update_N`
is used, it is idempotent.

## Out of scope

- Building the Daxko backend plugin (daxko handled only when that module is
  enabled — currently skipped, see W0b `DECISIONS.md`).
- Per-block UX beyond the fallback (P0 owns the selector).

## Result

(to be filled when phase ships)

Legacy global `settings.backend` honoured as the per-block fallback; no silent
Solr flip; handoff notes recorded in W0b `DECISIONS.md`.
