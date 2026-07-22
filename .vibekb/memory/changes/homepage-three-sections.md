---
id: homepage-three-sections
type: change
title: Distill homepage to three sections
summary: Collapsed the long story-driven homepage into three sections — the problem (with a three-beat arc), what you get (four pillars), and live proof + CTA — while keeping the developer-journey message and real carousel.
status: implemented
verification: verified-manually
updated: 2026-07-22
functionality: [load-living-model]
files: [index.php, assets/css/homepage.css, assets/js/homepage.js]
tags: [homepage, copy, change]
---

## Before

Eleven sections: six-beat journey, uncertainty questions, gap, solution, tabs, carousel,
before/after, workflow timeline, repo map, and separate CTA. Accurate but too long to scan.

## After

Three sections:

1. **The problem** — thesis, recognition copy, three-beat arc (ship / lose plot / fear change), live metrics card.
2. **What you get** — understanding layer + four pillar cards linking to guide views.
3. **See it work** — functionality carousel + CTA.

`homepage.js` trimmed to guide carousel only.
