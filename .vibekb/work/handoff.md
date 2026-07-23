---
id: handoff
type: handoff
title: Current handoff
summary: Homepage no longer advertises that install does not require Go or PHP. Download → vibekb install → coding agent flow is unchanged; PHP 8.2+ remains the post-install runtime.
updated: 2026-07-23
verification_state: verified-from-source
---

## Current state

Install and Compatibility copy state positive requirements (downloadable
executable, write access, PHP 8.2+ after install) without “No Go / no PHP
required to install” marketing lines. Advanced build-from-source still mentions
Go for contributors.

## Verification completed

- Grep of `index.php`: no “No Go”, “no PHP required”, “do not need Go”
- `php -l index.php`
- Rendered homepage still has `/releases/latest`, six assets, `vibekb install`
- `php tools/vibekb.php check` + generate (this commit)

## Unresolved / next

Tag `v0.1.0` was already pushed earlier; next product work remains code signing
(and other Phase 2 distribution items) when ready.

## Exact next recommended action

Plan code signing / further Phase 2 distribution (Homebrew, Winget, curl) per
RELEASE.md when ready.
