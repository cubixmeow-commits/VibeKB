---
id: handoff
type: handoff
title: Current handoff
summary: Phase 2a downloadable CLI releases are in place — tag v* builds six platform binaries with checksums and version ldflags; homepage/docs lead with GitHub Releases. Next: code signing, then Phase 2b package managers.
updated: 2026-07-23
verification_state: verified-from-source
---

## Current state

`vibekb` is packaged as a downloadable cross-platform CLI. Pushing a `v*` tag
runs `.github/workflows/release.yml` (test, vet, cross-compile, checksums,
GitHub Release). `vibekb version` reports Version / Commit / Built / Platform.
README, INSTALLER.md, and the homepage present Downloads as the primary path;
build-from-source is Advanced. The installer and PHP model engine are unchanged.

## Completed this change

- `internal/buildinfo`: Version, Commit, Built (ldflags overrides)
- `internal/cli/version.go` + test: identity block output
- `.github/workflows/release.yml`: six GOOS/GOARCH artifacts + `checksums.txt`
- `RELEASE.md`: publish commands and remaining signing notes
- README / INSTALLER / ARCHITECTURE Phase 2a/2b split
- Homepage: download → `vibekb install` → coding agent
- `install.php` migration notice points at Releases
- Self-model + `/docs` regenerated

## Verification completed

- `go test ./...`, `go vet`, gofmt clean
- Local cross-compile of all six artifact names + sha256sum
- ldflags smoke: `vibekb version` shows injected fields
- Homepage HTML: no primary `go build`; GitHub Releases + `vibekb install`
- `php tools/vibekb.php check --strict`; `test-topology.php`

## Not done yet

- **Code signing / notarization** (recommended next)
- **Phase 2b**: Homebrew, Winget, curl installer
- **Actually tagging `v0.1.0`** after this merges (manual maintainer step)

## Exact next recommended action

After merge: tag and push `v0.1.0` per `RELEASE.md`. Then plan Apple
notarization and Windows Authenticode before package-manager distribution.
