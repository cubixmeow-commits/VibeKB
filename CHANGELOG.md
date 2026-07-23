# Changelog

All notable changes to the **VibeKB product / CLI** are documented here.
Versions follow [SemVer](https://semver.org/) on the `0.x` line until 1.0.
The installer **template** version (`template/manifest.json` → `template_version`)
is a separate namespace and is not the same as this product version.

The format is based on [Keep a Changelog](https://keepachangelog.com/).

## [Unreleased]

## [0.2.0] — 2026-07-23

### Title

**VibeKB 0.2.0 — Safe Repository Integration**

This release changes how VibeKB installs into other repositories so existing
projects are treated as first-class: VibeKB no longer overwrites repository-owned
files by default, consolidates its own footprint under `.vibekb/`, and integrates
with agent instruction files only through namespaced adapters or a single marked
managed block.

### Added

- Ownership model and collision policy for every path VibeKB may write
  (documented in `docs/REPOSITORY_SAFETY.md`): VibeKB-owned under `.vibekb/` (+
  namespaced adapters), shared files via managed blocks only, repository-owned
  paths never modified automatically.
- Installer payload map (`template/manifest.json` schema 2 / `template_version`
  `2.0.0`) that remaps the embedded runtime into `.vibekb/runtime/`,
  `.vibekb/reference/`, and `.vibekb/prompts/`.
- Managed-block engine for shared files (`AGENTS.md`, `CLAUDE.md`): insert /
  update / remove; idempotent; CRLF/LF and trailing-newline aware; malformed or
  duplicate markers reported as conflicts (no silent rewrite).
- Namespaced adapters: `.cursor/rules/vibekb.mdc`,
  `.github/instructions/vibekb.instructions.md` (only when that tool directory
  already exists, unless `--integrate` forces them).
- Installation flags: `--dry-run`, `--knowledge-only` / `--no-integrations`,
  `--integrate <list>`, narrowed `--force`.
- Per-install manifest `.vibekb/install.json` (ownership, kind, hashes, block
  version, pre-existing flags; no absolute paths).
- `vibekb migrate` — consolidate a pre-2.0 root-level install under `.vibekb/`
  (content/title/hash-gated; ambiguous files preserved).
- `vibekb uninstall` — ownership-aware removal (`--keep-knowledge`, `--dry-run`);
  shared-file backups relocated out of `.vibekb/` on a full uninstall so they
  survive.
- Doctor repository-footprint diagnostics (legacy root layout, missing
  authoritative consolidated files, managed-block issues, install-manifest
  drift).
- Layout-aware PHP core (`guide/lib/workspace.php`) so the guide and tools work
  from either the self-hosted root layout or `.vibekb/runtime/`.
- Consolidated installs generate the static snapshot to `.vibekb/generated/`
  (not the target’s root `docs/`).
- Hardened tag release workflow: full Go + PHP test suite before publish,
  explicit asset verification matching `install.sh`, sorted SHA-256
  `checksums.txt`, `fail_on_unmatched_files`.

### Changed

- Default `vibekb install` no longer writes root-level `CLAUDE.md`, `AGENTS.md`,
  `PRODUCT.md`, `SCHEMA.md`, `INITIALIZE.md`, `MAINTENANCE.md`, `INSTALLER.md`,
  `guide/`, `tools/`, `prompts/`, or `template/` into target repositories.
- `--force` is narrowly scoped: take over an unrecognized `.vibekb/` and reset
  starter model files — it does **not** wholesale-overwrite shared files.
- Upgrade refreshes VibeKB-owned payload under `.vibekb/` and preserves the
  living model records.
- Product / CLI version is **0.2.0** (`0.2.0-dev` in unstamped local builds).

### Fixed

- `tools/generate-static.php` copies CSS/JS from the **runtime** guide assets so
  consolidated installs produce a working `.vibekb/generated/` snapshot.
- Legacy `CLAUDE.md` / `AGENTS.md` migration requires an exact first-line title
  plus signatures (lookalike user files are not replaced).
- Uninstall no longer deletes shared-file backups with `.vibekb/`; they are
  relocated to a stamped temp directory (or retained with `--keep-knowledge`).
- Release checksum generation no longer relies on an unsorted glob that could
  include `checksums.txt` itself.

### Migration notes

- **Fresh installs:** use `vibekb install .` (or the website curl installer, then
  `vibekb install .`). Everything VibeKB owns lands under `.vibekb/`.
- **Existing 0.1.0 / root-level installs:** run `vibekb migrate --dry-run .`,
  then `vibekb migrate .`. Only unmodified VibeKB root files are removed;
  modified or unrecognized files stay and are reported.
- **Agent instructions:** if `AGENTS.md` / `CLAUDE.md` already exist, install /
  migrate add or update a single managed block and leave the rest of the file
  alone. VibeKB does not create those files by default when they are absent.
- **After migrate:** prefer
  `php .vibekb/runtime/tools/vibekb.php …` (or `vibekb <cmd>` which discovers the
  runtime). Root `php tools/vibekb.php` remains valid in VibeKB’s own
  self-hosted repository.

### Compatibility notes

- Still on the `0.x` line — APIs and install layout may continue to evolve.
- Existing 0.1.0 layouts keep running (layout-aware PHP); migrate is optional but
  recommended to leave the repository root clean.
- `install.sh` (macOS/Linux arm64/amd64) downloads the same unix asset names as
  before (`vibekb-${GOOS}-${GOARCH}`). Windows binaries ship for manual download
  only.
- Installer `template_version` **2.0.0** is independent of product **0.2.0**.
- PHP 8.2+ is still required **after** install to run the guide and model
  commands; install itself does not require PHP or Go.

### Known limitations

- `vibekb doctor` diagnoses the current working directory only (extra path
  arguments are ignored); run it from inside the target repository.
- Doctor footprint checks assume the **consolidated** layout; VibeKB’s own
  self-hosted repo (root `guide/` / `tools/` + root reference docs) reports
  missing `.vibekb/runtime/…` files and root-doc warnings by design today.
- Per-file writes are atomic; a crash mid-install can still leave a partial
  `.vibekb/` tree (re-run install/upgrade or uninstall to reconcile).
- `install.sh` does not yet verify `checksums.txt` or code signatures.
- No Apple notarization / Windows Authenticode, Homebrew, or Winget packages
  yet.

## [0.1.0] — 2026-07-23

Initial tagged CLI release: cross-platform `vibekb` binaries via GitHub Actions,
`vibekb version` with link-time Version/Commit/Built, website `curl | sh`
installer, and native `vibekb install` from an embedded payload (pre-safety
consolidation footprint).

[Unreleased]: https://github.com/cubixmeow-commits/VibeKB/compare/v0.2.0...HEAD
[0.2.0]: https://github.com/cubixmeow-commits/VibeKB/compare/v0.1.0...v0.2.0
[0.1.0]: https://github.com/cubixmeow-commits/VibeKB/releases/tag/v0.1.0
