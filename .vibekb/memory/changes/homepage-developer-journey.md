---
id: homepage-developer-journey
type: change
title: Rewrite homepage around the developer journey story
summary: The homepage now follows the comic-inspired emotional arc (excitement → confusion → fear → VibeKB → confidence) with "AI helped you build it. VibeKB helps you understand it." as the spine — selling understanding, not documentation.
status: implemented
verification: verified-manually
updated: 2026-07-22
functionality: [load-living-model]
files: [index.php, assets/css/homepage.css, assets/js/homepage.js]
tags: [homepage, copy, journey, change]
---

## Before

The homepage explained what VibeKB records and how the repository model works —
accurate, but category-first. It did not lead with the moment a developer realizes
they shipped faster than they understood.

## After

- Hero and CTA centre on: AI helped you build it. VibeKB helps you understand it.
- Six-beat developer journey section with comic-inspired emotional pacing — balanced for vibe coders building their own software and developers extending open source.
- Uncertainty section names fear/guessing, not "documentation."
- VibeKB introduced only after the problem lands, as the missing understanding layer.
- Before/after transformation section; live proof carousel retained.
- Story dot navigation (progressive enhancement) in homepage.js.

## Impact

Visitors should recognize their own arc within the first scroll and describe
VibeKB as the thing that helps them understand the AI-built app they were afraid
to touch — not as a documentation generator.
