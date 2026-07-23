---
id: handoff
type: handoff
title: Current handoff
summary: Homepage install/compatibility copy now names Windows as a manual GitHub Releases path; curl | sh remains macOS/Linux-only. Ready for review/merge.
updated: 2026-07-23
verification_state: verified-from-source
---

## Current state

- Homepage `#install` and `#compatibility` no longer imply Windows is unsupported.
- Windows binaries remain release assets for **manual** download only
  (`vibekb-windows-amd64.exe` / `vibekb-windows-arm64.exe`).
- `install.sh` is unchanged (still refuses Windows and points at Releases).
- Product version remains **0.2.0** (already released on main); this is a copy fix.

## Completed work

- Updated `index.php` Installing / Current Requirements / step-1 manual link.
- README notes Windows manual binaries.
- Model: change record `homepage-windows-install-copy`, functionality UX text,
  important-files purpose/test hint, handoff/current.

## Verification

- Rendered homepage HTML contains Windows platform line, `.exe` download
  instructions, and updated manual link; old “macOS or Linux (arm64…” install
  card line removed.
- `php -l index.php` clean; no claim of a Windows curl/PowerShell/Winget installer.

## Unresolved / next

- Optional later: Windows PowerShell installer or Winget (roadmap; not this change).
- Authenticode signing still not done (release hardening).

## Exact next recommended action

Review and merge this PR so the live site stops omitting Windows from install
requirements.
