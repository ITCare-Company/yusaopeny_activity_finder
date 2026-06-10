# Psetup — Sandbox deploy (Vlad)

**Owner:** @svicervlad
**Blocks:** P0–P5 (Ira cannot start QA until test URL is live)

## Goal

Deploy the Vue 3 build from this PR branch to a publicly reachable environment
so Ira can run W6 QA without a local DDEV setup.

## Approach — ITCare CI sandbox (not local DDEV)

Sandboxes are **config-driven** in the CI repo, not hand-built DDEV instances.
A site config YAML in `ci.itcare.solutions.org/configs/sites/` is consumed by
Jenkins (`SITE_ENVIRONMENT_JOBS_BUILDER` → Provisioning → Build), which clones
`yusaopeny-project`, runs `pre_deploy_composer_commands`, then the
`deploy_drush_commands` install sequence on the `sandboxes` stack.

Modeled on the existing `carnation-cus-d9.sandboxes.y.org` config. Both AF
environments pull Activity Finder from the **ITCare-Company fork** via a VCS
repo override (`config repositories.activity_finder vcs ...`).

**CI config PR:** ci.itcare.solutions.org#605

| Env | URL | AF source | Backend | Purpose |
|---|---|---|---|---|
| stable | `sandbox-af4-migration.y.org` | fork `7.x` (Vue 2) | Solr | Vue 2 baseline reference |
| develop | `sandbox-af4-migration-dev.y.org` | fork `feat/ITCR-1273-w0-baseline` (Vue 3) | Mock (31 static sessions) | **W6 QA target for Ira** |

Why two backends: the fork ships `openy_activity_finder.settings backend: mock`
by default. **develop** keeps that (mock = no Solr, matches the W0 baseline
screenshots). **stable** explicitly `cset backend solr` for a real-data Vue 2
reference.

## Steps

1. Land the CI config PR (ci.itcare.solutions.org#605).
2. Jenkins: run `SITE_ENVIRONMENT_JOBS_BUILDER` for `af4-migration.sandboxes.y.org`.
3. Jenkins: run Provisioning, then Build for the `develop` env (Vue 3 QA target).
4. Verify `/activity-finder-v4` loads and shows mock results; modals open.
5. Share the `develop` URL with Ira (JIRA ITCR-1273 + Slack thread).
6. Record the URL in `queue/W6-qa-visual-functional/README.md` under **Test site**.

## Done when

- `sandbox-af4-migration-dev.y.org` is reachable by Ira (not localhost)
- Activity finder loads, shows mock results, modals open
- URL posted in W6 README + JIRA comment

## Out of scope

- Any code fixes — this is deploy-only
- Solr for the QA target (develop uses the Mock backend; only the stable
  baseline env runs Solr)
