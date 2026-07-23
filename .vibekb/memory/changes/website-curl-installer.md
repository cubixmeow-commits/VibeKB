---
id: website-curl-installer
type: change
title: Website curl installer as product CLI entry point
summary: Added install.sh (and /install rewrite) on the VibeKB website so users install with curl | sh; homepage primary path updated; GitHub Releases is the download backend and manual fallback only.
status: implemented
verification: verified-from-source
updated: 2026-07-23
functionality: [run-the-developer-cli, install-into-a-repository, initialize-in-a-repository, deploy-and-stay-portable]
files: [install.sh, .htaccess, index.php, assets/css/homepage.css, .cpanel.yml, DEPLOYMENT.md, RELEASE.md, README.md, INSTALLER.md, ARCHITECTURE.md, install.php]
tags: [homepage, installer, distribution, curl, change]
---

## Before

Ordinary users were told to open GitHub Releases, pick a platform asset, rename
it, chmod it, and put it on PATH. The website was marketing only for CLI
acquisition. curl installation was still listed under Coming soon.

## After

- `install.sh` at the site root: detect macOS/Linux + arm64/amd64, resolve the
  latest GitHub Release asset, install to `/usr/local/bin` or `~/.local/bin`,
  `chmod +x`, run `vibekb version`
- `.htaccess` internal rewrite so `/install` serves the same script
- Homepage step 1 is `curl -fsSL https://iainreid.dev/vibekb/install.sh | sh`
  with a secondary “Prefer to install manually?” Releases link
- README / INSTALLER / RELEASE / DEPLOYMENT / ARCHITECTURE / install.php notice
  updated; Coming soon no longer lists curl installation
- Designed so checksums, signing checks, Homebrew/Winget can be added later
  without changing the user-facing curl command

## Verification note

`sh -n install.sh` clean. End-to-end smoke against the live `v0.1.0` release
installed `vibekb-linux-amd64` into a temp dir and `vibekb version` succeeded.
Rendered homepage contains the curl one-liner as the primary install command and
does not use “Download latest release” as the primary CTA.
