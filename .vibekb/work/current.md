---
id: current-work
type: work
title: Public website under /website/
objective: Build the public website VibeKB deserves — a serious, original developer-tool site (not a generic AI SaaS page) whose signature visual is an interactive Live Repository Map generated from VibeKB's own model.
summary: Complete. A self-contained static site lives at /website/ (index.html + assets/{css,js,data}). The hero centrepiece is an interactive functionality map drawn from real model data; proof uses the real Stoppr integration audit. No application functionality changed; the site is a presentation surface.
requested_by: User (website design & positioning brief)
status: complete
verification_state: verified-from-source
updated: 2026-07-23
affected_functionality: []
expected_files: [website/index.html, website/assets/css/site.css, website/assets/js/map.js, website/assets/js/site.js, website/assets/data/model.js, website/README.md, .vibekb/work/handoff.md, .vibekb/work/current.md]
data_impact: None — new static marketing site; application code, installer, and .vibekb model of functionality are unchanged.
risks:
  - website/assets/data/model.js is a hand-maintained snapshot; refresh it when VibeKB's own model changes.
  - Keep it a presentation surface — do not model the marketing site as application functionality (product is locked).
---

## Status

Complete. `/website/` is a static, no-build-step site built around the Live
Repository Map (real areas/capabilities/edges from `.vibekb/`), with the Stoppr
integration audit as real proof. Verified across mobile/tablet/desktop in headless
Chromium: interactive map ≥ 720px, accessible fallback below that and without
JavaScript, no horizontal overflow, side panel shows real record data.
`php tools/vibekb.php check` → OK. See the handoff for the "not modelled as
functionality" decision.
