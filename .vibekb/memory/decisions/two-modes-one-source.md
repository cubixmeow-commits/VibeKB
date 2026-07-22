---
id: two-modes-one-source
type: decision
title: Two output modes over one source and one template set
summary: The dynamic guide (Mode A) and the static snapshot (Mode B) render the same `.vibekb/` through the same templates; the only difference is the URL strategy.
status: accepted
verification: verified-from-source
updated: 2026-07-22
functionality: [render-guide, generate-static-snapshot, deploy-and-stay-portable, search-the-model]
files: [guide/lib/nav.php, guide/lib/UrlStrategy.php, tools/generate-static.php]
tags: [architecture, rendering]
---

## Context

VibeKB needs a live, always-current view for people working in the repo, and a
publishable static site (GitHub Pages) that needs no PHP. The naive approach —
two renderers — guarantees they drift.

## Decision

One loader, one template set, one shared navigation/route/title definition
(`nav.php`). The static generator swaps only the URL strategy (relative,
subpath-safe links) and writes the rendered pages to disk. There is no second
template system.

## Alternatives considered

- **A separate static-site generator / different templates** — rejected: two
  renderers drift, and the public site would stop being a true demonstration of
  the model.

## Reason

If the public `/docs` site is literally the live guide frozen at a commit, it is
honest proof of the product, and "it works in the guide" implies "it works in
/docs."

## Consequences

- Adding a view means editing `nav.php` once; both modes pick it up.
- The drift check can compare `/docs` to a fresh render to detect staleness.
- The search index is built by one function for both modes.
