---
id: homepage-voice-pass
type: change
title: Homepage voice pass (indie-developer tone)
summary: Rewrote homepage marketing prose to sound like an experienced indie developer explaining a tool they built, without changing layout, CSS, commands, installation instructions, or product claims.
status: implemented
verification: verified-from-source
updated: 2026-07-23
functionality: [initialize-in-a-repository, deploy-and-stay-portable, install-into-a-repository]
files: [index.php]
tags: [homepage, copy, tone, change]
---

## Before

Homepage copy was accurate but read like software-marketing: punchy arc labels
(“Lose the plot”, “Fear the next change”), “understanding layer”, and
advertisement-adjacent CTAs.

## After

Same section order, commands, and claims. Prose uses shorter sentences and a
calm developer voice (README / indie launch post). Examples: the three-beat arc
became “You ship quickly / The app outgrows your map of it / Every change
starts to feel like a guess”; the product headline became “A living knowledge
base that stays with your repository.”

## Honesty preserved

Install commands, Go vs PHP requirements, Coming soon items, and the
installer-does-not-analyze boundary are unchanged.

## Verification note

Diffed `index.php` to confirm command variables and install command strings
were unchanged. `php -l index.php` passed. Copy-button clicks were not
exercised in a browser here.
