# Psetup — Sandbox deploy (Vlad)

**Owner:** @svicervlad
**Blocks:** P0–P5 (Ira cannot start QA until test URL is live)

## Goal

Deploy the Vue 3 build from this PR branch to an accessible environment so Ira
can run W6 QA without a local DDEV setup.

## Steps

1. Spin up a DDEV sandbox with YUSAOpenY + activity finder from branch
   `feat/ITCR-1273-w0-baseline`.
2. Import mock sessions (31 static sessions — no Solr needed, mock backend).
3. Verify `/activity-finder-v4-layout-builder` loads and shows results.
4. Share the URL with Ira (post in JIRA ticket / Slack thread).
5. Record the URL in `queue/W6-qa-visual-functional/README.md` under **Test site**.

## Done when

- URL is reachable by Ira (not localhost)
- Activity finder loads, shows mock results, modals open
- URL posted in README + JIRA comment

**Status: DONE** — https://af4-vue3.itcaresolutions.org/activity-finder-v4-layout-builder

## Out of scope

- Any code fixes — this is deploy-only
- Solr setup (mock backend is sufficient for W6)
