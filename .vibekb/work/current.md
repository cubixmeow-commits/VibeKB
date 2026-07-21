---
id: current-work
type: work
title: Integrate reusable lessons from the StopPR field test
summary: Port the generalizable product, interface, generation, provenance, and visualization improvements proven by the VibeKB-stoppr deployment back into the canonical VibeKB product, without importing StopPR application content.
objective: Upgrade VibeKB's product, templates, initialization workflow, rendered guide, and canonical example to reflect the strongest lessons from the StopPR deployment while keeping the promise "Understand what your software is doing."
requested_by: Project owner
status: completed
verification_state: verified-manually
updated: 2026-07-21
affected_functionality: []
expected_files:
  - docs/STOPPR_INTEGRATION_AUDIT.md
  - guide/lib/helpers.php
  - guide/lib/Content.php
  - guide/lib/Provenance.php
  - guide/lib/UrlStrategy.php
  - guide/templates/*.php
  - guide/assets/js/guide.js
  - tools/generate-static.php
  - tools/validate.php
  - .vibekb/diagrams/*
  - .vibekb/manifest.json
  - prompts/INTEGRATE_VIBEKB.md
  - SCHEMA.md, INITIALIZE.md, MAINTENANCE.md, README.md, PRODUCT.md, DEPLOYMENT.md, AGENTS.md, CLAUDE.md
data_impact: None to SousMeow or StopPR (both read-only). VibeKB product + content only.
risks:
  - Turning VibeKB into a generic documentation generator or static-site theme (explicitly out of bounds).
  - Importing StopPR application content (Flutter/Firebase/subscription specifics) into the canonical example.
  - Two divergent renderers (PHP guide vs static docs) drifting apart.
  - Overstating freshness — implying the static snapshot auto-updates.
---

## What the user asked for

Analyze the canonical VibeKB repository and the real-world VibeKB-stoppr
field test, then integrate the reusable product, interface, documentation,
generation, provenance, and visualization improvements from StopPR back into
the main VibeKB product. Audit first; copy nothing blindly. Preserve the
product definition, the SousMeow canonical example, PHP 8.2 shared-hosting
compatibility, subfolder deployment, and all truth/provenance rules.

## Current architecture (before)

- `.vibekb/` is the repository-owned source of truth (functionality-first).
- A PHP 8.2 guide (`guide/`) renders `.vibekb/` dynamically via query-string
  routing. No static output. No diagrams. No search. jQuery + Google Fonts
  loaded from external CDNs. Overview mislabels the functionality-record total
  as "areas" and shows an undefined "Last meaningful update".

## Proposed architecture (after)

- Two output modes over the **same** `.vibekb/` source and the **same**
  templates:
  - **Mode A — Dynamic guide:** the existing PHP renderer (cPanel / local PHP).
  - **Mode B — Static snapshot:** `tools/generate-static.php` renders the same
    templates through a static URL strategy into `/docs` for GitHub Pages and
    any static host. Clearly labelled as a source-commit snapshot; no implied
    auto-freshness.
- A shared **provenance** component (source repo/branch/commit, generation
  time, mode, verification scope, freshness disclaimer) on both modes.
- A first-class **diagrams** model in `.vibekb/diagrams/` (records + repo-owned
  SVGs with accessible `<title>`/`<desc>`), rendered in both modes and
  cross-linked to functionality and warnings.
- Consistent count vocabulary: **functional areas** vs **functionality records**
  vs **status counts**, with validation against contradictory totals.
- Client-side search (vanilla JS over a generated `search.json`) and
  client-side functionality filters; no external services, no jQuery, no
  required CDN.

## Affected VibeKB functionality

VibeKB's own guide app (the product), not the SousMeow example behaviour. The
SousMeow model is updated only enough to demonstrate the new capabilities
(provenance block, a small accurate diagram set, count clarity).

## Migration approach

Audit → provenance/count/schema → diagrams model → static generator + URL
strategy → search/nav → initialization workflow + prompt → canonical example
refresh + validation → docs + verification + handoff. Focused commits per
concern.

## Validation plan

- `php -l` across `guide/` and `tools/`.
- `php tools/validate.php` (headless validator) reports zero errors.
- Dynamic guide loads every view; Reference shows no validation errors.
- `php tools/generate-static.php` produces `/docs`; every generated internal
  link resolves; assets are relative (subpath-safe); SVGs are well-formed XML
  with title/desc; search.json entries point to existing pages; no absolute
  local filesystem paths leak; no StopPR content in the canonical example.
