---
id: homepage-drop-no-go-php-claims
type: change
title: Simplify homepage install requirements copy
summary: Homepage install and compatibility copy no longer advertise that installation does not require Go or PHP; download → install → agent steps and post-install PHP 8.2+ requirements remain.
status: implemented
verification: verified-from-source
updated: 2026-07-23
functionality: [install-into-a-repository, initialize-in-a-repository, deploy-and-stay-portable]
files: [index.php, assets/css/homepage.css]
tags: [homepage, installer, copy, change]
---

## Before

Install lead, step cards, a badge (“No Go and no PHP required to install”), and
Compatibility / Current Requirements listed negative “does not require Go/PHP”
claims alongside the real downloadable-binary workflow.

## After

- Removed lead sentences about not needing Go / PHP-only-afterward
- Removed “Ordinary users do not need to install Go” from step 1
- Removed the install-step badge and unused `.hp-install-req` CSS
- Removed step-2 “You do not need it to run vibekb install”
- Compatibility Installing and Current Requirements Installation no longer list
  “No Go…” / “No PHP process required to install”
- Positive post-install PHP 8.2+ requirements under Running / Using after
  installation are unchanged

## Verification note

Grep of `index.php` found no remaining “No Go”, “no PHP required”, or
“do not need Go” phrasing. `php -l index.php` clean. Rendered HTML still shows
releases/latest, six platform assets, and `vibekb install`.
