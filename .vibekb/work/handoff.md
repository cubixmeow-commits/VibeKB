---
id: handoff
type: handoff
title: Current handoff
summary: Homepage install copy now matches the downloadable release workflow (releases/latest, six platform assets, Installing vs Running after vs Advanced source build). PHP 8.2+ remains the post-install runtime. Next: tag v0.1.0, then code signing.
updated: 2026-07-23
verification_state: verified-from-source
---

## Current state

Ordinary users are told to download from GitHub Releases (`/releases/latest`),
pick the correct `vibekb-*` asset, run `vibekb install`, then use a coding agent.
Go appears only under Advanced. PHP 8.2+ is clearly a post-install requirement.

## Verification completed

- Asset names on homepage match `.github/workflows/release.yml` exactly
- `php -l index.php`; rendered checks for install.php / Native CLI / doctor absent
- `go test ./...`, `go vet ./...`
- `php tools/vibekb.php check --strict` (after generate)

## Exact next recommended action

Tag `v0.1.0` per `RELEASE.md` when ready, then plan code signing.
