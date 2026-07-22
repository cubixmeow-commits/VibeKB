---
id: current-work
type: work
title: First-class VibeKB installer
objective: Add install.php, a bootstrap command, a template payload manifest, and a shared starter library so adopting VibeKB is one command; reconcile the self-hosted model and docs.
summary: Installer + bootstrap + starter library added and reconciled into the model. See handoff.md.
requested_by: cubix.meow@gmail.com
status: complete
verification_state: verified-from-source
updated: 2026-07-22
affected_functionality: [install-into-a-repository, bootstrap-workspace, initialize-in-a-repository]
expected_files: [install.php, tools/lib/Starter.php, tools/vibekb.php, template/manifest.json]
data_impact: None — the installer reads the target repository and writes only VibeKB's own files (runtime payload + a fresh .vibekb/). It never modifies application code.
risks: [The template payload can drift from what a target needs if template/manifest.json is not kept in step with repository structure.]
---

## Status

Complete and reconciled into the model. The installer prepares the workspace; an
AI agent still builds the model. See `.vibekb/work/handoff.md`.
