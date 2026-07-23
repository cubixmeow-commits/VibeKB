---
id: homepage-windows-install-copy
type: change
title: Homepage names Windows as a manual install path
summary: Compatibility and install copy now list Windows alongside macOS/Linux. The curl | sh installer remains macOS/Linux; Windows users download vibekb-windows-*.exe from GitHub Releases.
status: implemented
verification: verified-from-source
updated: 2026-07-23
functionality: [run-the-developer-cli, install-into-a-repository, initialize-in-a-repository, deploy-and-stay-portable]
files: [index.php, README.md]
tags: [homepage, compatibility, windows, releases, copy, change]
---

## Before

The Installing VibeKB card and Current Requirements listed only macOS or Linux,
even though release.yml already publishes `vibekb-windows-amd64.exe` and
`vibekb-windows-arm64.exe` for manual download. Visitors inferred Windows was
unsupported.

## After

- Platforms: macOS, Linux, or Windows (arm64 or amd64).
- Website curl installer: still macOS/Linux (`curl`/`wget`).
- Windows: download the `.exe` from GitHub Releases, rename to `vibekb.exe`, PATH.
- Install step 1 manual link text names Windows explicitly.
- README notes Windows binaries are manual-download only.

## Honesty preserved

No Windows `install.sh`, PowerShell installer, Winget, or Authenticode claim.
`install.sh` still dies on Windows with a Releases pointer.
