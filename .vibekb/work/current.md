---
id: current-work
type: work
title: Make vibekb install truly native (no PHP required to install)
objective: Move installation into the Go CLI so `vibekb install` copies the runtime and scaffolds .vibekb/ from an embedded payload, with no PHP process and no live source clone — while leaving the PHP runtime, guide, model loader, and generator untouched.
summary: Complete — native Go installer with embedded payload; starter model turned into shared data (template/starter/); install.php is now a wrapper; model reconciled. See handoff.md.
requested_by: cubix.meow@gmail.com
status: complete
verification_state: verified-from-source
updated: 2026-07-23
affected_functionality: [install-into-a-repository, bootstrap-workspace, run-the-developer-cli]
expected_files: [embed.go, internal/installer/installer.go, internal/installer/console.go, install.php, tools/lib/Starter.php, template/starter/starter.json, template/manifest.json]
data_impact: None to the runtime. Installation writes into target repositories only (payload + fresh .vibekb/ + .installer.json); the guide, model loader, and generator are unchanged.
risks: [Duplicating the starter definition (avoided — one template/starter/ read by Go and PHP); requiring PHP to install (removed — native, verified with PHP off PATH); breaking bootstrap on targets (avoided — template/starter/ is installed); breaking the self-hosted-repo guard (kept — refuses self_hosted models).]
---

## Status

Complete. See `.vibekb/work/handoff.md`.

## Decisions recorded

`decision:native-installer-embedded-payload` — installation is native to the Go
binary, from an embedded payload; PHP is required only to run the guide.
`decision:installer-template-not-duplicated-tree` updated for the embed + starter
data mechanism. Full assessment in `ARCHITECTURE.md`.
