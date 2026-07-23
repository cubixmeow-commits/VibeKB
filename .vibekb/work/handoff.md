---
id: handoff
type: handoff
title: Current handoff
summary: Added a self-contained static marketing site under /website/, built around an interactive "Live Repository Map" drawn from VibeKB's own model. It is a presentation surface, not modelled application functionality. Ready for review.
updated: 2026-07-23
verification_state: verified-from-source
---

## Current state

- New static site at `/website/` (`index.html` + `assets/{css,js,data}`). Plain
  HTML/CSS/vanilla JS, no build step, subpath-safe, works without JavaScript.
- Its hero centrepiece is the **Live Repository Map**: an interactive SVG of
  VibeKB's own 8 functional areas and their relationships, drawn from real model
  data in `website/assets/data/model.js`.
- The existing PHP homepage (`index.php`) is unchanged. `/website/` is a parallel,
  fully static presentation of the product; it does not replace `index.php`.

## Completed work

- `website/assets/data/model.js` — real areas/capabilities transcribed from
  `.vibekb/functionality/` and the `vibekb-architecture` topology; cross-area
  edges derived from record `depends_on` links.
- `website/assets/js/map.js` — the map (progressive enhancement over an accessible
  fallback list), `website/assets/js/site.js` — copy buttons.
- `website/index.html` + `website/assets/css/site.css` — the full homepage.
- `website/README.md` — deployment and honesty notes.

## Verification

- Rendered in headless Chromium at 390 / 860 / 1280px: map builds and is
  interactive ≥ 720px, the accessible fallback shows below that and with JS off,
  no horizontal overflow at any width, side panel populates with real record data.
- Proof figures (Stoppr 30 records / 10 areas / 21 diagrams / 6 systems / commit
  `d5fc37c`) are transcribed from `examples/field-tests/STOPPR_INTEGRATION_AUDIT.md`.
- Map/stat totals (23 functionalities, 8 areas, 6 systems, 29 files, 45
  relationships, 3 diagrams) are the real current totals of this model.
- `php tools/vibekb.php check` → OK (0 errors). `website/` is reported as an
  unmapped changed path — see decision below.

## Decision: `/website/` is intentionally not a functionality record

Like `index.php`, the marketing site is a *presentation surface*, not application
functionality VibeKB models. Adding it to the functionality model would push
VibeKB toward being a website builder, against the locked product definition. The
`check` "unmapped changed source" note for `website/` is expected and reviewed,
not an omission. If the site later grows genuinely new *product* behaviour, revisit.

## Unresolved / next

- `website/assets/data/model.js` is a hand-maintained snapshot of the model.
  Refresh it (and the figures in `index.html`) whenever VibeKB's own areas,
  capabilities, files, or verification states change.

## Exact next recommended action

Review the `/website/` site (open `website/index.html`), then merge.
