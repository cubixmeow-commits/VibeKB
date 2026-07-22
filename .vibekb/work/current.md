---
id: current-work
type: work
title: Homepage install fast-start
objective: Replace the homepage's buried install copy with a three-step fast-start section (clone → install → Cursor prompt) placed directly under the hero, matching the real installer workflow.
summary: Complete — homepage #install redesigned and moved beneath the hero; model reconciled. See handoff.md.
requested_by: cubix.meow@gmail.com
status: complete
verification_state: verified-from-source
updated: 2026-07-22
affected_functionality: [install-into-a-repository, initialize-in-a-repository, load-living-model]
expected_files: [index.php, assets/css/homepage.css, assets/js/homepage.js]
data_impact: None — marketing homepage only. Does not change installer behaviour or the living model content of a target install.
risks: [Homepage commands could drift from install.php / INSTALLER.md if not checked against the real installer; copy must not imply automatic repository understanding.]
---

## Status

Complete. The homepage shows the real public install workflow under the hero
without claiming the installer understands the application. See
`.vibekb/work/handoff.md`.
