---
id: handoff
type: handoff
title: Current handoff
summary: Homepage hero now shows the optimized developer-journey comic beside section-1 copy; three-section structure unchanged. Next: keep model reconciled as VibeKB changes.
updated: 2026-07-22
verification_state: verified-manually
---

## Completed this change

- Resized/renamed hero image to `assets/images/homepage-developer-journey.webp` (+ PNG fallback).
- Hero layout: copy left; journey comic above live metrics card in the right column on desktop.
- Responsive stack on narrow screens.

## Verification completed

- `php -l index.php`; image assets on disk; `php tools/vibekb.php check` OK; `/docs` regenerated.

## Exact next recommended action

`php tools/vibekb.php status` before the next change; `affected` → update model → `check` + `generate` before commit.
