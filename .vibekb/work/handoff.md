---
id: handoff
type: handoff
title: Current handoff
summary: The public homepage (index.php) was restructured from eleven sections to seven and re-positioned as "repository understanding for AI-assisted development." PHP-as-product-category framing removed; the self-demo is now framed as VibeKB analyzing its own repository. Dynamic metrics and the functionality carousel still come from real .vibekb/ records. Next: keep the model reconciled as VibeKB changes; optionally author explainable topology for self-maintenance-loop.
updated: 2026-07-22
verification_state: verified-manually
---

## What the software (VibeKB) now does

VibeKB remains self-hosted and its runtime behaviour is unchanged — this was a
copy / information-architecture pass on the landing page only. The homepage now
leads with the category ("Understand the software AI helped you build"), is
organized around one spine ("AI can change six files faster than you can rebuild
your mental model"), and presents one four-part model: current functionality, how
it works, active AI work, and repository memory. The hero example card and the
functionality carousel still render from the live `.vibekb/` model.

## Completed this change

- Rewrote `index.php`: seven sections (hero · problem · what it adds · live proof ·
  workflow · why repository-owned · CTA), new `<title>`/meta description, updated
  nav and footer.
- Removed the PHP-as-product-category framing; relabelled the hero card to
  "VibeKB analyzing its own repository"; moved all PHP/runtime details into a
  collapsed "How this reference implementation runs" note.
- Consolidated the repeated "what it does / how / what AI is changing" blocks and
  reduced the seven-principle manifesto to three trust principles.
- Retained the interactive tabs, carousel, workflow timeline, and repo map;
  removed the redundant stepper, depth, relevance, compare, and manifesto widgets.
  `assets/js/homepage.js` and `assets/css/homepage.css` were left unchanged (removed
  widgets' binds early-return safely; reused existing CSS classes only).
- Added `docs/HOMEPAGE_COPY_AUDIT.md` and `docs/HOMEPAGE_REWRITE_REPORT.md`
  (process artifacts; the static generator preserves `docs/*.md`).

## Verification completed

- `php -l index.php` clean.
- Rendered `index.php` against loaded `.vibekb/`: hero + carousel + all seven
  sections present; hero shows 17 functions modelled (matches 17 records); no
  PHP-as-category language in the body.
- Degraded paths exercised: missing `manifest.json` renders normally; missing
  `.vibekb/` renders without a fatal error (carousel omitted; hero card degrades
  to zeroed metrics).
- All guide links relative (`guide/?view=…`) — subfolder-safe; no absolute hrefs.
- `php tools/vibekb.php check` and `php tools/test-topology.php` run before finish;
  `/docs` regenerated.

## Active warnings (VibeKB)

- `model-can-drift-from-code`, `docs-is-generated-never-hand-edit`,
  `verification-must-reflect-evidence`.

## Honest limitations / not verified

- Homepage verified by rendering the PHP against loaded content and reading the
  markup, not by a full browser screenshot or a mobile-device render in this
  environment (CSS was unchanged and uses the existing responsive rules).
- Cursor discovery remains `inferred`; live cPanel host not exercised here.

## Exact next recommended action

For any future change, start with `php tools/vibekb.php status`, use `affected`
to find impacted records, update them, and finish with `check` + `generate`
before committing. Optionally author explainable topology for
`self-maintenance-loop` if richer visuals are wanted.
