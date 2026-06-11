# Cachet Brandbook — Activity Finder

> The Cachet typeface is the YMCA's primary brand font. This brandbook codifies how
> Cachet is defined, loaded, and applied across YMCA Website Services (Open Y) so the
> Activity Finder design stays on-brand and consistent with the surrounding site.
>
> Sources: reverse-engineered from `openy_carnation`, `y_lb`, `openy_font`, and the
> `ws_small_y` / `lb_*` module ecosystem (YMCA Website Services, "small-y" profile).

---

## 1. The typeface

| Property | Value |
| --- | --- |
| Family name | `Cachet` |
| Role | Brand display + UI font (headings, buttons, nav, labels, badges) |
| Body counterpart | `Verdana` (long-form copy is **not** set in Cachet) |
| Licensing | Commercial — licensed per-site from fonts.com. **Not** redistributable, so it is **not** shipped as a web font in the repo. |
| Delivery | Self-hosted TTFs uploaded by the site owner via the `openy_font` module, injected as `@font-face` at runtime |

### Weight system

Cachet ships in three weights. This is the canonical mapping used across the platform:

| Weight | `font-weight` | Cachet name | Typical use |
| --- | --- | --- | --- |
| Book | `400` | `Cachet W01 Book` | Body-adjacent UI, nav, breadcrumbs, filter tags, default buttons |
| Medium | `500` | `Cachet Medium` | Headings (h1/h2/h5/h6), form labels, card links, event badges, CTAs |
| Bold | `700` | `Cachet Bold` | Strong card titles, emphasis |

> `openy_carnation` uses one `800` (extra-bold) instance on the global-search pager.
> Treat it as a legacy outlier — **do not** introduce new `800` usages; Cachet has no
> matching physical weight, so the browser synthesizes it.

---

## 2. How Cachet is loaded (the injection chain)

Cachet is never hard-loaded by a module or the Vue app. The chain is:

```
openy_font module (admin uploads Cachet Bold/Book/Medium TTFs)
   └─ hook_page_attachments() injects @font-face into <style> on the default theme
        └─ y_lb library defines :root CSS custom properties
              --ylb-font-family-cachet : Cachet, Verdana, Geneva, sans-serif
              --ylb-font-family-book   : "Cachet Book", "Cachet W01 Book", Cachet, Verdana, sans-serif
              --ylb-font-family-medium : "Cachet Medium", Cachet, Verdana, sans-serif
              --ylb-font-family-verdana: Verdana, Geneva, sans-serif
                   └─ components consume var(--ylb-font-family-cachet, Cachet)
```

**Consequence for Activity Finder:** the AF Vue app must *consume* the `--ylb-*`
custom properties, never declare its own `@font-face` or hard-code `Cachet`. When the
host site has uploaded the font, AF inherits it automatically; when it has not, the
fallback chain degrades gracefully to Verdana.

### Canonical font stacks

```css
/* Cachet (default brand UI) */
font-family: var(--ylb-font-family-cachet, Cachet), Verdana, sans-serif;

/* Cachet Book (400) */
font-family: var(--ylb-font-family-book, "Cachet W01 Book"), Verdana, sans-serif;

/* Cachet Medium (500) */
font-family: var(--ylb-font-family-medium, "Cachet Medium"), Verdana, sans-serif;

/* Body / long-form copy */
font-family: var(--ylb-font-family-verdana, Verdana), sans-serif;
```

---

## 3. Type scale (from `openy_carnation`)

Reference scale the Activity Finder should align to. Cachet headings are **uppercase**
with **tight negative tracking**.

| Element | Weight | Size | Letter-spacing | Transform | Color |
| --- | --- | --- | --- | --- | --- |
| h1 | 500 | 50px | -2px | uppercase | `#2f2f2f` (gray-700) |
| h2 | 500 | 40px | -2px | uppercase | `#2f2f2f` |
| h3 | 400 | 30px | -0.5px | — | inherit |
| h4 | 400 | 24px | -0.5px | — | `#636466` (gray-400) |
| h5 | 500 | 20px | -0.5px | — | inherit |
| h6 | 500 | 20px | — | — | inherit |
| Body | — (Verdana) | base | — | — | inherit |

> Headings carry **negative letter-spacing** (`-0.5px` to `-2px`) — this is a defining
> trait of the YMCA look. Buttons/labels often add `text-transform: uppercase`.

---

## 4. Element application map

How each UI primitive uses Cachet in the YMCA design language. The Activity Finder
should mirror this so its controls feel native inside an Open Y page.

| UI element | Cachet weight | Notes |
| --- | --- | --- |
| Page / step titles (h1–h2) | 500 | uppercase, `-2px` tracking |
| Sub-titles (h3–h4) | 400 | `-0.5px` tracking |
| Buttons (default) | 400 (`cachet-book`) | uppercase on CTAs |
| Buttons (CTA / primary) | 500 | uppercase, e.g. LTO button 18px |
| Primary / mobile nav | 400 | mixed transform |
| Breadcrumbs | 400 | 20px, no transform |
| Form labels | 500 | 14px, `#4f4f4f` (gray-500) |
| Form controls / selects | 400 | 14px, uppercase on selects |
| Filter tags / pills | 400 | 12px, uppercase, 30px pill |
| Card titles | 500 / 700 | bold for branch/location names |
| Event date badges | 500 | uppercase, tight tracking |
| Status / alert messages | 500 | 18px |
| Body copy / descriptions | — | **Verdana**, not Cachet |

> **Rule of thumb:** structural and interactive chrome → Cachet; reading content
> (paragraphs, result descriptions) → Verdana.

---

## 5. Brand color pairing

Cachet typography is paired with the Y brand palette (exposed as `--ylb-color-*` /
`--y-color-*` custom properties). The Activity Finder already defines an aligned
palette in `openy_af4_vue_app/src/scss/_variables.scss`.

| Token | Hex | Pairs with |
| --- | --- | --- |
| Primary blue | `#0060af` (`$af-blue`) | links, primary buttons |
| Light red / brand red | `#ed1c24` (`$af-light-red`) | accents |
| Purple | `#92278f` (`$af-violet`) | section headings, captions |
| Teal | `#006b6b` | breadcrumb links |
| Pink | `#c6168d` | badges, pager, event date |
| Dark gray | `#2f2f2f` / `#636466` | heading text |
| White | `#fff` | text on dark/brand backgrounds |

The `$af-ages` map in `_variables.scss` already encodes the age-group brand colors used
on result cards — keep Cachet labels legible against each (contrast checked per pair).

---

## 6. Applying this to Activity Finder (7.x)

### Current state
- `openy_af4_vue_app/src/scss/_variables.scss` defines **colors and border-radius only** —
  there are **no font tokens**.
- Vue components hard-code `font-family: var(--ylb-font-family-cachet, Cachet), Verdana, sans-serif;`
  inline, repeated across `Step.vue`, `SelectPath.vue`, `Fieldset.vue`, `Results.vue`, etc.
- AF CSS is loaded via the `theme` category, so a host theme can override it.

### Recommended direction (to be implemented in follow-up PRs)
1. **Add font tokens to `_variables.scss`** so the inline `var(...)` duplication has a
   single source of truth:
   ```scss
   $af-font-cachet:  var(--ylb-font-family-cachet, Cachet), Verdana, sans-serif;
   $af-font-medium:  var(--ylb-font-family-medium, "Cachet Medium"), Verdana, sans-serif;
   $af-font-book:    var(--ylb-font-family-book, "Cachet W01 Book"), Verdana, sans-serif;
   $af-font-body:    var(--ylb-font-family-verdana, Verdana), sans-serif;
   ```
2. **Replace hard-coded `font-family` declarations** in the Vue SFCs with these tokens.
3. **Align the type scale** of AF step/result titles to §3 (weight 500, uppercase,
   negative tracking) so headings match the surrounding Open Y page.
4. **Never add `@font-face`** to the AF app — rely on `openy_font` + `y_lb` injection.
5. **Keep result/description body copy in Verdana**, headings/labels/badges in Cachet.
6. **Accessibility:** because Cachet may be absent (unlicensed sites), verify every
   layout against the Verdana fallback — line lengths and button widths must not break.

---

## 7. Do / Don't

**Do**
- Consume `--ylb-font-family-*` custom properties.
- Use 400/500/700 only.
- Uppercase + negative tracking on display headings and CTAs.
- Test with and without Cachet present.

**Don't**
- Ship or `@import` Cachet font files (licensing).
- Hard-code `font-family: Cachet` without the `--ylb-*` var and Verdana fallback.
- Set body/reading copy in Cachet.
- Introduce new `800` weight usages.
