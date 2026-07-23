---
id: handoff
type: handoff
title: Current handoff
summary: Product CLI install is now curl | sh from iainreid.dev/vibekb/install.sh (GitHub Releases behind the scenes). Homepage primary path updated. Next after deploy: confirm /install.sh and /install on the live host; then code signing.
updated: 2026-07-23
verification_state: verified-from-source
---

## Current state

- `install.sh` is the website installer (macOS/Linux, arm64/amd64 → latest
  release asset → `/usr/local/bin` or `~/.local/bin` → `vibekb version`).
- `.htaccess` rewrites `/install` to the same script.
- Homepage step 1 is the curl one-liner; Releases is a secondary manual link.
- Docs (README, INSTALLER, RELEASE, DEPLOYMENT, ARCHITECTURE) match.

## Verification completed

- `sh -n install.sh`
- End-to-end smoke against live `v0.1.0` (linux/amd64) into a temp dir;
  `vibekb version` OK
- Homepage render: curl command primary; no primary Download-latest-release CTA
- `php tools/vibekb.php check` + generate (this commit)

## Unresolved / next

Live cPanel deploy must publish `install.sh` and `.htaccess`. Confirm both URLs
on https://iainreid.dev/vibekb/ after deploy. Code signing remains the next
hardening milestone; checksum verification can be added to `install.sh` without
changing the curl command.

## Exact next recommended action

Deploy to cPanel, then `curl -fsSL https://iainreid.dev/vibekb/install.sh | head`
(and `/install`) to confirm the script is served; plan code signing.
