---
id: homepage-hero-image
type: change
title: Homepage hero journey image
summary: Optimized and renamed the developer-journey comic; placed beside hero copy in section 1 with responsive layout.
updated: 2026-07-22
verification_state: verified-manually
---

## What changed

- Source asset `assets/7E1D04F9-6324-4E51-AF19-65328ABA0308.png` (~1.4 MB on main) was resized and renamed to `assets/images/homepage-developer-journey.webp` (~19 KB) with a PNG fallback (~52 KB).
- `index.php` section 1: hero comic spans full width above a two-column row (copy | metrics).
- `homepage.css`: two-column grid on desktop; image centered above; stacks on narrow viewports.

## Verification

- Image files present at expected paths; `php -l index.php`; `php tools/vibekb.php check` clean.
