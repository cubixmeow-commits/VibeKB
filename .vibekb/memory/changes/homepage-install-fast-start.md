---
id: homepage-install-fast-start
type: change
title: Homepage install fast-start under the hero
summary: The homepage now places a three-step install section (clone → install.php → Cursor prompt) directly under the hero, with copy buttons, a result strip that labels docs/ as generated later, and an expandable installer disclosure — without implying automatic repository understanding.
status: implemented
verification: verified-from-source
updated: 2026-07-22
functionality: [install-into-a-repository, initialize-in-a-repository, load-living-model]
files: [index.php, assets/css/homepage.css, assets/js/homepage.js]
tags: [homepage, installer, onboarding, copy, change]
---

## Before

A short install block sat between "What you get" and live proof. It mixed a
combined command block with a six-step list that mentioned upgrades and
`bootstrap`, burying the real public workflow.

## After

Page flow is: hero/problem → **Add VibeKB to your repository** → what you get →
live proof. The install section shows exactly three primary steps matching
`INSTALLER.md` / `install.php`:

1. `git clone https://github.com/cubixmeow-commits/VibeKB.git`
2. `php VibeKB/install.php /path/to/your/project` (plus example path; PHP 8.2+)
3. A copyable Cursor prompt pointing at `prompts/INTEGRATE_VIBEKB.md`

Copy buttons live in `homepage.js`. The result strip distinguishes installed
runtime (`.vibekb/`, `guide/`, `tools/`) from `docs/` (generated later via
`php tools/vibekb.php generate`). A disclosure covers what the installer does,
the `template/manifest.json` payload, and optional `--dry-run`.

## Honesty preserved

The section states that the installer prepares VibeKB and Cursor understands the
application. It does not claim automatic analysis, completed functionality
records, or self-updating models.

## Verification note

Commands and rendered section order were checked against source and a PHP render of `index.php`. Copy-button clicks were not exercised in a browser in this environment.

