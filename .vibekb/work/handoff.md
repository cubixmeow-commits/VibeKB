---
id: handoff
type: handoff
title: Current handoff
summary: Homepage install copy matches the native Go CLI, and a follow-up voice pass rewrote marketing prose into a calmer indie-developer tone without changing commands, layout, or claims. Next: Phase 2 distribution (release binaries + brew/winget/curl).
updated: 2026-07-23
verification_state: verified-from-source
---

## Current state

The homepage describes the real `vibekb install` path (clone → `go build` →
`./vibekb install` → coding agent) and reads in a plain developer voice. PHP is
not required to install; PHP 8.2+ remains required afterward for the guide and
model commands. Layout, CSS, and install commands were not changed by the voice
pass.

## Completed this change

- `index.php` prose voice pass only (hero, install blurbs, compatibility,
  what-you-get, proof, footer).
- `change:homepage-voice-pass`; current work + handoff; provenance; `/docs`
  regenerated.

## Verification completed

- Command variables and rendered install/dry-run strings unchanged vs prior
  commit.
- `php -l index.php`
- `php tools/vibekb.php check --strict`
- `php tools/test-topology.php`
- `go test ./...` (unchanged Go code; re-run for safety)

## Not done yet (roadmap)

- **Phase 2 — distribution**: release binaries, `curl | sh`, Homebrew, winget.
- **Phase 3 — shared core boundary**
- **Phase 4 — evaluate a bundled PHP runtime**

## Exact next recommended action

`php tools/vibekb.php status` before the next change. Continue with Phase 2
distribution when ready.
