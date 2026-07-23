---
id: homepage-releases-install-copy
type: change
title: Homepage install copy for GitHub Releases download flow
summary: Homepage install and requirements now match the downloadable-binary workflow from release.yml: releases/latest link, real platform asset names, Installing vs Running after vs Advanced build-from-source, no Go for ordinary users, PHP 8.2+ still post-install.
status: implemented
verification: verified-from-source
updated: 2026-07-23
functionality: [install-into-a-repository, initialize-in-a-repository, deploy-and-stay-portable]
files: [index.php]
tags: [homepage, installer, releases, copy, change]
---

## Before

Homepage already pointed at Releases after Phase 2a, but used `/releases` (not
`/latest`), did not list release asset filenames, and folded install/runtime
requirements into one card without a secondary build-from-source path.

## After

- Download button → `…/releases/latest`
- Step 1 lists the six assets from `.github/workflows/release.yml`
- Requirements split: Installing VibeKB / Running after installation / Advanced build from source
- Badge: “No Go and no PHP required to install”
- Coming soon unchanged (signing, Homebrew, Winget, curl)

## Verification note

Rendered HTML checked against release.yml asset names; `vibekb install` matches
installer usage; no `install.php` or primary `go build` in install cards.
