# Activity Finder — Solr backend

Provides the **`solr`** `ActivityFinderBackend` plugin (Search API / Solr) for
[Open Y Activity Finder](../../README.md), plus the Search API server, index and
index processors it needs. Enable this submodule only when you want Activity
Finder to serve **real indexed content**. For local UI / theme / front-end work
the main module's default **Mock** backend is enough — you do **not** need Solr.

## What it provides

- `Drupal\openy_activity_finder_solr\Plugin\ActivityFinderBackend\SolrBackend`
  — the `solr` backend plugin (wraps `OpenyActivityFinderSolrBackend`).
- The 8 Search API processors that index Activity Finder fields
  (`af_ages_min_max`, `af_duration`, `af_parts_of_day`, `af_start_month`,
  `af_weekdays_parts_of_day`, …).
- Config (`config/optional`): `search_api.server.solr`,
  `search_api.index.default`.

## Requirements

- `search_api` + `search_api_solr`.
- A reachable Solr server with a core for this site's index.
- Activity Finder **content with sessions** — Solr has nothing to return until
  session content exists and is indexed (see "Demo content" below).

## Setup

1. **Enable the submodule** (installs the server + index config):

   ```sh
   ddev drush en openy_activity_finder_solr -y
   ```

2. **Point the `solr` server at your core.** The shipped
   `search_api.server.solr` config ships the generic `collection1`/empty
   connector; set it to your environment's core. With the `ddev/ddev-drupal-solr`
   add-on (core `dev`):

   ```sh
   ddev drush php:eval '$c=\Drupal::configFactory()->getEditable("search_api.server.solr");$b=$c->get("backend_config");$b["connector_config"]["core"]="dev";$b["connector_config"]["host"]="solr";$b["connector_config"]["port"]=8983;$b["connector_config"]["solr_install_dir"]="/opt/solr";$c->set("backend_config",$b)->save();'
   ddev drush cr
   ddev drush php:eval '$s=\Drupal\search_api\Entity\Server::load("solr"); var_dump($s->getBackend()->isAvailable());'  # must be TRUE
   ```

   The core itself (schema `drupal-*-solr-8.x`) is created by the add-on. On
   **remote DDEV** the host `.ddev/solr` mount does not reach the remote
   container — generate the config to an **absolute** path, `docker cp` the zip
   out, unzip, `docker cp` the conf into the solr container and `bin/solr create
   -c dev -d <conf>`. See the workspace CLAUDE.md "SOLR SETUP" / "Solr-config
   gotcha".

3. **Select the Solr backend.** Set the global default at
   `/admin/openy/settings/activity-finder` (Backend → Solr) or override per-block
   in the AF4 block form. Existing sites are switched automatically by the main
   module's `hook_update_N`.

## Demo content (sessions)

Solr returns nothing without session content. Seed the Open Y demo sessions:

```sh
# GOTCHA: on a fresh small_y, `pm:enable openy_demo_nsessions` fails (exit 1) —
# openy_demo_ncamp pulls config openy_demo_node_camp_news, which needs
# openy_node_news. Enable openy_node_news FIRST, on its own, then nsessions:
ddev drush pm:enable openy_node_news -y
ddev drush pm:enable openy_demo_nsessions -y

# Migrate the demo sessions (35 nodes). If a prior run left a stale lock
# ("migration ... is busy"), reset it first:
ddev drush migrate:reset-status openy_demo_node_branch
ddev drush migrate:import openy_demo_node_session_01 --execute-dependencies -y

ddev drush search-api:index default -y
```

Then verify (count > 0):

```sh
ddev exec "curl -s 'http://localhost/af/get-data?backend%5B%5D=solr'"
```

## Notes

- The backend response shape is the shared contract documented in
  [`../../fixtures/schema/runProgramSearch.schema.json`](../../fixtures/schema/runProgramSearch.schema.json);
  the Solr backend is its reference implementation.
- Removing this submodule leaves the main module on Mock (or any other enabled
  backend) — Activity Finder keeps working without Solr.
