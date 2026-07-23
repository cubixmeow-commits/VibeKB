---
id: current-work
type: work
title: Update homepage for the native Go installer
objective: Make the public homepage accurately describe the native `vibekb install` workflow (clone → build Go CLI → install → coding agent), remove outdated PHP-install and Coming soon claims, strip em dashes from homepage copy, and keep the PHP post-install runtime distinction clear.
summary: Complete — homepage matches INSTALLER.md native Go flow; PHP required only after install; Native CLI / Repository doctor removed from Coming soon.
requested_by: Cursor cloud agent task
status: complete
verification_state: verified-from-source
updated: 2026-07-23
affected_functionality: [install-into-a-repository, initialize-in-a-repository, deploy-and-stay-portable]
expected_files: [index.php, .vibekb/work/current.md, .vibekb/work/handoff.md, .vibekb/memory/changes/homepage-native-installer-copy.md, .vibekb/files/important-files.json, .vibekb/manifest.json, .vibekb/project/identity.md, .vibekb/functionality/records/initialize-in-a-repository.md]
data_impact: None to runtime behaviour. Homepage marketing copy and self-model records only; `/docs` regenerated.
risks: [Implying VibeKB is entirely Go (avoided — PHP kept as post-install runtime); inventing brew/winget/curl commands (avoided — Coming soon only).]
---

## Status

Complete. See `.vibekb/work/handoff.md`.

## Decisions / changes recorded

`change:homepage-native-installer-copy` — homepage install copy matches the
native Go installer; requirements and Coming soon reconciled with ARCHITECTURE.md
Phase 2.
