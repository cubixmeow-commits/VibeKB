---
id: release-binaries-pipeline
type: change
title: Downloadable cross-platform vibekb release binaries
summary: Added a tag-triggered GitHub Actions release that cross-compiles vibekb for six platforms with stripped ldflags, Version/Commit/Built injection, checksums.txt, and a GitHub Release. Homepage and docs now present Downloads as the primary install path; build-from-source is Advanced. Installer and PHP runtime unchanged.
status: implemented
verification: verified-from-source
updated: 2026-07-23
functionality: [run-the-developer-cli, install-into-a-repository, initialize-in-a-repository, deploy-and-stay-portable]
files: [.github/workflows/release.yml, internal/buildinfo/buildinfo.go, internal/cli/version.go, RELEASE.md, README.md, INSTALLER.md, ARCHITECTURE.md, index.php, install.php, template/manifest.json]
tags: [cli, go, release, distribution, homepage, change]
---

## Before

Developers were told to clone VibeKB and `go build` to get `vibekb`. Version
output was a one-line `vibekb 0.1.0-dev`. No automated release workflow existed.

## After

Pushing `v*` tags runs `.github/workflows/release.yml`: test/vet, cross-compile
six GOOS/GOARCH pairs with `CGO_ENABLED=0` + `-trimpath` + `-ldflags "-s -w …"`,
write `checksums.txt`, create a GitHub Release with notes and assets.
`vibekb version` prints Version / Commit / Built / Platform. Docs and homepage
lead with Downloads; source builds are Advanced. Next recommended hardening:
code signing; then Homebrew/Winget/curl (Phase 2b).

## Honesty preserved

Installer and PHP model engine unchanged. PHP 8.2+ still required after install.
No brew/winget/curl commands are presented as available.

## Verification note

Local cross-compile of all six artifacts succeeded; ldflags smoke test confirmed
`vibekb version` fields. Workflow not yet run against a real tag in this session
(requires push of `v0.1.0` after merge). Homepage HTML checked for absence of
primary `go build` path.
