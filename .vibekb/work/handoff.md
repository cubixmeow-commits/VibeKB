---
id: handoff
type: handoff
title: Current handoff
summary: Installation is now fully native to the `vibekb` Go binary — it embeds the runtime payload and a canonical starter definition and installs with no PHP and no live clone. The starter model became shared data (template/starter/) read by both Go and PHP; install.php is a thin wrapper. Next: distribution (release binaries + brew/winget/curl).
updated: 2026-07-23
verification_state: verified-from-source
---

## Current state

`vibekb install` is native Go. It parses the embedded `template/manifest.json`,
copies the runtime payload from the binary's embedded filesystem, and scaffolds a
fresh `.vibekb/` from the embedded `template/starter/` definition — launching no
PHP. The PHP runtime is unchanged: the model loader (`guide/lib`), the dynamic
guide (Mode A), the static generator (Mode B), `bootstrap`, and validation are
exactly as before, and `php tools/vibekb.php …` still works.

## Completed this change

- **Starter as data**: `template/starter/` (a `starter.json` directory list + a
  `files/` tree with `{{DATE}}` / `{{PROJECT_NAME_JSON}}` tokens) is now the one
  canonical starter definition. Generated from the old `Starter.php` so output is
  byte-identical.
- **Starter.php refactored** to read `template/starter/` — `bootstrap` unchanged,
  no duplicated starter content. `template/starter/` is installed into targets so
  `bootstrap` keeps working there.
- **Native installer**: `embed.go` (module root) embeds the manifest, payload, and
  starter; `internal/installer` does manifest parsing, planning, embedded-FS copy,
  native scaffold, native verify, and `.installer.json`.
- **install.php → wrapper** that forwards to `vibekb install` (or prints how to
  get the binary). One installer implementation.
- **CLI**: `install` is native; obsolete source-clone/PHP delegation for install
  removed from `internal/phpcore` and `doctor`.
- **Model reconciled**: `install-into-a-repository`, `bootstrap-workspace`, and
  `run-the-developer-cli` records; `decision:native-installer-embedded-payload`;
  `change:native-go-installer`; the not-duplicated-tree decision updated;
  provenance, current-work, handoff; `/docs` regenerated.

## Verification completed

- `go build/vet/test` pass; `gofmt` clean.
- Native install with **PHP removed from PATH** into a scratch repo → success,
  61 runtime files + 17 scaffold files, `.installer.json` written.
- On that installed target (with PHP): `php tools/vibekb.php bootstrap` →
  "Everything is in place"; `php tools/validate.php` → 0 errors.
- Native scaffold is **byte-identical** to PHP's scaffold.
- Installer paths exercised: dry-run (no writes), upgrade (preserve .vibekb/),
  `--force` (resets a tampered model), self-hosted-repo refusal, install.php
  wrapper (forwards when a binary is present; guidance when not).
- `php tools/vibekb.php check --strict`; `test-topology.php`; `/docs` regenerated.

## Not done yet (roadmap)

- **Phase 2 — distribution**: cross-compiled release binaries + checksums, a
  `curl | sh` installer, a Homebrew tap, and a winget manifest, so
  `brew install vibekb` becomes real. Now unblocked — install is self-contained.
- **Phase 3 — shared core boundary**: lift the model core out of `guide/lib`.
- **Phase 4 — evaluate a bundled PHP runtime** so the guide too needs nothing
  preinstalled.

## Exact next recommended action

`vibekb status` (or `php tools/vibekb.php status`) before the next change. To
continue, start Phase 2: add release automation (goreleaser-style cross-compiles)
and a `curl | sh` script that fetches the right `vibekb` binary. Keep the
boundaries: the installer stays PHP-free and embedded; the model loader/generator
stay the single PHP implementation.
