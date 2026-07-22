---
id: homepage-tagline-self-hosting
type: change
title: Replace leftover SousMeow homepage tagline with VibeKB self-hosting copy
summary: The homepage hero card no longer falls back to an old workflow-platform tagline; it uses VibeKB's identity one_liner/summary, and adjacent framing now describes VibeKB explaining itself.
status: implemented
verification: verified-manually
updated: 2026-07-22
functionality: [load-living-model]
files: [index.php, .vibekb/project/identity.md]
tags: [homepage, self-hosting, copy, change]
---

## Before

The homepage hero card read `identity.meta.one_liner` only. VibeKB's identity had
a `summary` but no `one_liner`, so the card fell back to a hardcoded SousMeow-era
sentence ("A platform for running step-by-step AI workflows…"). Nearby copy still
framed the active model as a separate sample application.

## After

- Identity carries a VibeKB `one_liner`.
- The homepage prefers `one_liner`, then `summary`, with a VibeKB-accurate
  fallback (same pattern as the guide overview).
- When `self_hosted` / non-sample, the hero card, functionality carousel, and
  final CTA describe VibeKB explaining itself.
- `index.php` is recorded in important-files so future `affected` runs map it.

## Impact

Visitors see an accurate product description on the landing page. The guide
overview also surfaces the new one_liner.
