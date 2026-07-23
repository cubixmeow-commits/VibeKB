---
id: prepare-version-0-2-0
type: change
title: Prepare product version 0.2.0 — Safe Repository Integration
summary: Product/CLI version 0.2.0 with repository-safety install footprint, changelog, and GitHub release notes. Prevents overwriting repository-owned files; consolidates VibeKB under .vibekb/; managed-block and namespaced integrations; migrate/uninstall/doctor; hardened release workflow. Tag/release not cut in this change.
status: implemented
verification: verified-from-source
updated: 2026-07-23
functionality: [run-the-developer-cli, install-into-a-repository, migrate-legacy-install, uninstall-from-a-repository, generate-static-snapshot]
files: [internal/buildinfo/buildinfo.go, RELEASE.md, .github/workflows/release.yml, CHANGELOG.md, docs/RELEASE_NOTES_0.2.0.md, .vibekb/functionality/records/run-the-developer-cli.md, internal/buildinfo/buildinfo_test.go]
tags: [cli, go, release, version, repository-safety, changelog, change]
---

## Before

The only published product version was `v0.1.0`. Default install wrote root-level
docs and runtime into target repositories. There was no root `CHANGELOG.md`.

## After

- Product/CLI version **0.2.0** (`0.2.0-dev` locally).
- Safe repository integration (see `docs/REPOSITORY_SAFETY.md`).
- [`CHANGELOG.md`](../../CHANGELOG.md) records 0.2.0 / 0.1.0.
- [`docs/RELEASE_NOTES_0.2.0.md`](../../docs/RELEASE_NOTES_0.2.0.md) is the
  paste-ready GitHub Release body (title: **VibeKB 0.2.0 — Safe Repository
  Integration**).
- Installer `template_version` remains **2.0.0** (separate namespace).

## Verification note

Local pre-release validation (format, lint, full tests, install scenarios,
migrate, doctor) passed. Final release audit against `origin/main` passed
(install writes, embeds, asset lockstep, versions, changelog, Actions, tests,
generated docs, junk/secret scan) — **READY TO RELEASE**. Tag and GitHub
Release are deliberately not created until asked.
