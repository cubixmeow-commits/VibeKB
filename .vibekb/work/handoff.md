---
id: handoff
type: handoff
title: Current handoff
summary: StopPR's reusable lessons are integrated into VibeKB (static /docs mode, diagrams, provenance, honest counts, search) with the SousMeow example preserved. Next, trace the remaining inferred SousMeow areas and regenerate the snapshot.
updated: 2026-07-21
verification_state: mixed
---

## What the software (VibeKB) now does

VibeKB renders one `.vibekb/` source through one template set in two modes: the
dynamic PHP guide (Mode A) and a static `/docs` snapshot (Mode B, via
`tools/generate-static.php`) for GitHub Pages. Diagrams are first-class
source-grounded records; every rendering shows objective provenance and never
implies auto-freshness; counts distinguish functional areas from functionality
records; search and functionality filters run client-side with no jQuery and no
required CDN. The canonical example is still **SousMeow**, updated only to
demonstrate the new capabilities.

## What was integrated from the StopPR field test

Static `/docs` output; a maintainable PHP generator (StopPR had none); a
`.vibekb/diagrams/` records model with a small accurate SousMeow SVG set; a
shared provenance component; client-side search + filters in vanilla JS; a
Diagrams nav item. See `docs/STOPPR_INTEGRATION_AUDIT.md` for the full
decision record and what was deliberately excluded.

## SousMeow functionality state (unchanged this pass)

- **Verified from source:** discovery, auth, the full Runner
  (run-recipe → build-prompt → paste-response → review-quality-checks →
  approve-and-version), export-project-kit, admin overview, routing/security,
  database access.
- **Partial:** `reset-password` (SMTP-dependent web flow); `demo-simulation`
  (paste-example verified; bulk inferred).
- **Inferred from source:** `manage-account`, `seed-and-sync-content`, the
  Router, and some Model queries. No verification state was upgraded for the
  diagrams — inferred paths are labelled in them.

## Provenance note (important)

The SousMeow model was derived read-only and is not bundled into VibeKB. It can
go stale — re-verify against the SousMeow source
(`cubixmeow-commits/dev-portfolio-v2`, `projects/sousmeow`) before changing any
functionality claim. The `manifest.json` `provenance` block records the analysed
commit and that the output does not auto-update.

## Verification completed this pass

- `php -l` clean across `guide/` and `tools/`.
- `php tools/validate.php` → 0 errors, 0 warnings (25 records, 9 areas, 5
  diagrams).
- Dynamic guide: every view returns 200; Reference shows no validation errors.
- Static `/docs`: 57 pages; 1544 internal links resolve (0 broken, 0 absolute
  filesystem-path leaks); 97 search entries resolve; pages serve 200 under a
  repository subpath; SVGs are valid XML with `<title>`/`<desc>`.

## Active warnings (SousMeow)

- `read-write-path-coupling`, `pasted-response-is-untrusted`,
  `password-reset-depends-on-smtp`, `legacy-category-column`. The
  `risk-uncertainty-map` diagram summarises these.

## Exact next recommended action

Trace `app/Controllers/AccountController.php`, `scripts/seed.php`, and the
`Simulation*` services in the SousMeow source; promote `manage-account`,
`seed-and-sync-content`, and `demo-simulation` from inferred to verified (or
correct them) and update the `request-flow` diagram's Router step. Then run
`php tools/validate.php` and `php tools/generate-static.php` to refresh `/docs`.
