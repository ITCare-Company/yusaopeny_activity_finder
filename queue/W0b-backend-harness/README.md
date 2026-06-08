# W0b — Backend plugin system & local-dev harness (unblocks migration)

Make AF4's data backend **pluggable** so the app can **run and be developed
without a Solr stack**. Today the backend is a single hardwired service; this
wave turns it into a Drupal plugin type with selectable implementations (Solr,
Mock, DB) and a per-block backend selector.

This wave exists because the Vue 2 → Vue 3 migration cannot start cleanly
otherwise: AF4 only renders with a working backend + content, and standing up
Solr for every dev iteration is the blocker. A **Mock backend removes it** —
run AF4 from fixtures, iterate on the migration, no infrastructure.

It also gives W0-P1 a reproducible "before" to screenshot, and gives Ira a
running app to QA against (W6).

> **What this wave does and does NOT unblock.**
> - **Does:** local dev + the migration itself without Solr (Mock), real
>   content without Solr (DB), a reproducible baseline (W0-P1).
> - **Does NOT:** automated tests. The current AF4 markup is non-semantic
>   (`<div>`/`<span>` `@click`, zero `tabindex` — see RULES "Semantic markup").
>   You cannot write meaningful keyboard/a11y/DOM tests against it. The markup
>   gets reworked **during** the Vue 3 migration (the semantic-markup pass is
>   part of the per-component work), so **automated tests are future work, after
>   the migration** — not gated by this wave. **Current QA = Ira, manual.**
>
> **After `P2 mock-plugin` ships, the migration (W1+) can begin.**

---

## What exists today (measured on `6.x`)

| Piece | File | Shape |
|---|---|---|
| Backend contract | `src/OpenyActivityFinderBackendInterface.php` | interface — `runProgramSearch`, `getLocations`, `getSortOptions`, `getAges`, … |
| Base | `src/OpenyActivityFinderBackend.php` | shared base class |
| Solr impl | `src/OpenyActivityFinderSolrBackend.php` | the only implementation |
| Wiring | `openy_activity_finder.services.yml` | **hardwired** service `openy_activity_finder.solr_backend` |
| Consumers | `src/Controller/ActivityFinderController.php`, `src/Plugin/Block/ActivityFinder4Block.php` | inject the fixed service directly |

The interface is the right seam — it already abstracts the backend. The gap is
that selection is hardwired in `services.yml`; there is no discovery and no
way to pick a different implementation per block.

## Target

- A **Drupal plugin type** `ActivityFinderBackend` — discovery via the **Drupal
  plugin system** (attribute `#[ActivityFinderBackend(...)]`, or annotation on
  older cores), a plugin manager, base class implementing
  `OpenyActivityFinderBackendInterface`.
- Backends become plugins: **Solr** (extracted, no behaviour change), **Mock**
  (fixtures, zero infra), **DB** (entity/db query, no Solr).
- **Backend chosen in the block config form** (`ActivityFinder4Block`
  `blockForm`/`blockSubmit`) — a select listing discovered plugins, stored in
  block configuration, resolved at render via the plugin manager. Default
  stays Solr → existing sites unchanged.

## Phases

| Phase | Goal | Status |
|---|---|---|
| P0 plugin-manager | Define the `ActivityFinderBackend` plugin type (manager + attribute/annotation + base) and the block-config selector. Solr stays default. | pending |
| P1 solr-plugin | Move the existing Solr backend behind the new plugin (no behaviour change; default plugin id). | pending |
| P2 mock-plugin | Mock backend plugin — static fixtures, **zero Solr/DB**. Unblocks local dev + the migration (W1+). **Not** auto-tests (markup blocks those until after migration). | pending |
| P3 db-plugin | DB backend plugin — entity/database query, **no Solr** (real content). | pending |
| P4 demo-content | Seed demo content (`migrate:import openy_demo_node_session …`) so Solr/DB backends and the sandbox baseline have data. | pending |

See [`DECISIONS.md`](DECISIONS.md) for the plugin-id scheme, the config storage
key, and the Mock-vs-DB ordering rationale.

## Gating

- **W0-P0** (Drupal contract) should land first — the plugin manager must not
  alter the consumer contract (block id, library, mount).
- **W0b gates W0-P1** — no reproducible baseline without a runnable backend +
  content.
- Default backend stays **Solr**; this wave is purely additive at the consumer
  boundary. No existing site changes behaviour unless an operator switches the
  block's backend.

## Out of scope

- Vue migration (W3+). This wave is **PHP-side only** and runs on the current
  Vue 2 app.
- Rewriting the search algorithm. Each plugin faithfully implements the
  existing `OpenyActivityFinderBackendInterface`; no new search semantics.
- AF3 / Camp Finder backends.
