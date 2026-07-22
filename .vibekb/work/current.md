---
id: current-work
type: work
title: Homepage install fast-start
objective: Replace the homepage's buried install copy with a three-step fast-start section (clone → install → Cursor prompt) placed directly under the hero, matching the real installer workflow.
summary: In progress — homepage #install section redesigned and moved beneath the first introductory blocks; copy buttons and disclosure added; model reconciliation pending verification.
requested_by: cubix.meow@gmail.com
status: in-progress
verification_state: not-verified
updated: 2026-07-22
affected_functionality: [install-into-a-repository, initialize-in-a-repository]
expected_files: [index.php, assets/css/homepage.css, assets/js/homepage.js]
data_impact: None — marketing homepage only. Does not change installer behaviour or the living model content of a target install.
risks: [Homepage commands could drift from install.php / INSTALLER.md if not checked against the real installer; copy must not imply automatic repository understanding.]
---

## Requested outcome

Visitors understand how to install VibeKB within a few seconds of reading the
hero: clone VibeKB, run `php VibeKB/install.php /path/to/your/project`, then ask
Cursor to build the first model. Preserve the honest boundary — the installer
prepares the workspace; the coding agent interprets the software.

## Current vs proposed

**Current:** A short install section sits between "What you get" and live proof,
with a combined command block and a six-step list that mixes upgrades/bootstrap
into the first experience.

**Proposed:** A compact three-card sequence directly under the hero (Clone →
Install → Ask Cursor), success strip distinguishing installed vs generated
paths, an expandable "What does the installer do?" disclosure (with dry-run),
copy buttons, and CTAs to GitHub + INSTALLER.md.

## Verification plan

- Confirm displayed commands match `install.php` / `INSTALLER.md`.
- `php -l index.php` and `php -l install.php`.
- `php tools/vibekb.php check`, `php tools/test-topology.php`,
  `php tools/vibekb.php generate`.
- Confirm placement, anchors, no-JS readability, and that `/docs` is labelled
  as generated after the first model is built.
