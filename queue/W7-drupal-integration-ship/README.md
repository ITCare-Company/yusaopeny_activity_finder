# Wave W7 — Drupal integration & ship

Produce the production build, prove it satisfies the W0-P0 Drupal contract,
smoke it on a live Open Y site, and open the PR.

**Depends on W6 (parity confirmed).** Final wave.

## Phases

| Phase | Goal | Status |
|---|---|---|
| P0 | Production build; verify UMD global + CSS + `libraries.yml` contract. | pending |
| P1 | drush smoke on a live Open Y site. | pending |
| P2 | Open PR to the fork. | pending |

## Done when

The rebuilt `dist/` loads on a real Drupal site, AF4 works end-to-end, and a
PR is open against `6.x`.

## Out of scope

- Upstreaming to YCloudYUSA (separate decision once the fork is proven).
- AF3 / Camp Finder.

## Onboarding (UA — Lera)

W7 — фінал: збираємо продакшн-білд, перевіряємо, що Drupal підхоплює його так
само як раніше, тестуємо на живому сайті, відкриваємо PR.
