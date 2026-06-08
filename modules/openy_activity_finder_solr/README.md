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

2. **Point Activity Finder at Solr.** Either set the global default at
   `/admin/openy/settings/activity-finder` (Backend → Solr), or override
   per-block in the AF4 block form. Existing sites are switched automatically by
   the main module's `hook_update_N`.

3. **Create / refresh the Solr core** for the `search_api.server.solr` connector
   (core `dev` by default). With the `ddev/ddev-drupal-solr` add-on:

   ```sh
   ddev drush search-api-solr:get-server-config solr /tmp/sc.zip
   # copy the generated conf into the core and (re)create it, then:
   ddev drush search-api:server-status solr   # must be available
   ```

   > **Remote DDEV (docker on a remote host) gotcha.** The host `.ddev/solr`
   > mount does not reach the remote container, so editing it does nothing.
   > Generate the config to an **absolute** path, `docker cp` the zip out, unzip,
   > `docker cp` the conf into the solr container and `bin/solr create -c dev -d
   > <conf>`. See the workspace CLAUDE.md "SOLR SETUP" / "Solr-config gotcha".

## Demo content (sessions)

Solr returns nothing without session content. Seed the Open Y demo sessions:

```sh
ddev drush en openy_demo_nsessions -y
ddev drush migrate:import openy_demo_node_session_01 --execute-dependencies -y
ddev drush search-api:index
```

Then verify:

```sh
ddev exec "curl -s 'http://localhost/af/get-data?backend%5B%5D=solr'"   # count > 0
```

## Notes

- The backend response shape is the shared contract documented in
  [`../../fixtures/schema/runProgramSearch.schema.json`](../../fixtures/schema/runProgramSearch.schema.json);
  the Solr backend is its reference implementation.
- Removing this submodule leaves the main module on Mock (or any other enabled
  backend) — Activity Finder keeps working without Solr.
