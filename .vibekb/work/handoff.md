---
id: handoff
type: handoff
title: Current handoff
summary: Homepage install copy now matches the native Go CLI (clone → build → vibekb install → coding agent). PHP is not required to install; PHP 8.2+ remains the post-install runtime. Native CLI and Repository doctor are no longer listed as Coming soon. Next: Phase 2 distribution (release binaries + brew/winget/curl).
updated: 2026-07-23
verification_state: verified-from-source
---

## Current state

The public homepage (`index.php`) describes the real installation path from
`INSTALLER.md` / `README.md`: build `vibekb` from Go source, run
`./vibekb install /path/to/project` (no PHP), then ask a coding agent to build
the model. Compatibility cards split “to install from source” (Go 1.24+, Git,
write access) from “after installation” (PHP 8.2+, AI coding agent). Coming soon
lists only Phase 2 distribution items. Em dashes were removed from homepage
marketing copy and the identity one_liner shown on the hero card.

The installer implementation itself is unchanged: native Go with embedded
payload; PHP remains the guide and model-engine runtime.

## Completed this change

- `index.php`: install command variables, three install cards, Install/Current
  Requirements, Coming soon, installer `<details>`, and em-dash cleanup.
- Self-model: `change:homepage-native-installer-copy`;
  `initialize-in-a-repository` trigger/copy; `important-files.json` depends_on;
  related_memory links; identity one_liner/summary; handoff + current work;
  provenance.

## Verification completed

- Homepage HTML rendered via PHP: no `install.php` / “Requires PHP 8.2+” install
  card / “Native CLI” / “Repository doctor” strings; presents
  `./vibekb install`, `go build -o vibekb`, “Native installer: no PHP required”,
  Go 1.24+, and Phase 2 Coming soon badges.
- Commands checked against `INSTALLER.md`, `go.mod`, and
  `internal/installer` (`vibekb install [options] [target]`).
- `php -l index.php`; `php tools/vibekb.php check --strict`;
  `php tools/test-topology.php`; `go test ./...` (recorded in the session).

## Not done yet (roadmap)

- **Phase 2 — distribution**: cross-compiled release binaries + checksums, a
  `curl | sh` installer, a Homebrew tap, and a winget manifest.
- **Phase 3 — shared core boundary**: lift the model core out of `guide/lib`.
- **Phase 4 — evaluate a bundled PHP runtime**.

## Exact next recommended action

`php tools/vibekb.php status` before the next change. To continue product work,
start Phase 2 distribution. Keep boundaries: installer stays PHP-free and
embedded; model loader/generator stay the single PHP implementation.
