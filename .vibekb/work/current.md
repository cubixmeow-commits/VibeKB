---
id: current-work
type: work
title: Release binaries pipeline (Phase 2a)
objective: Add a tag-triggered GitHub Actions release that cross-compiles vibekb for six platforms with checksums and version ldflags, and present Downloads as the primary install path on the homepage and docs — without Homebrew/Winget/curl yet, and without changing the installer or PHP runtime.
summary: Complete — release workflow, buildinfo Version/Commit/Built, docs + homepage Downloads-first.
requested_by: Cursor cloud agent task
status: complete
verification_state: verified-from-source
updated: 2026-07-23
affected_functionality: [run-the-developer-cli, install-into-a-repository, initialize-in-a-repository, deploy-and-stay-portable]
expected_files: [.github/workflows/release.yml, internal/buildinfo/buildinfo.go, internal/cli/version.go, RELEASE.md, README.md, INSTALLER.md, ARCHITECTURE.md, index.php, install.php, template/manifest.json]
data_impact: None to install/runtime behaviour. Packaging, docs, and homepage only.
risks: [Implying Go still required for normal install (avoided); inventing brew/winget (avoided).]
---

## Status

Complete. See `.vibekb/work/handoff.md`.

## Change recorded

`change:release-binaries-pipeline`
