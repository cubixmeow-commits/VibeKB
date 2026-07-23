---
id: handoff
type: handoff
title: Current handoff
summary: Repository-safety redesign shipped. `vibekb install .` now writes everything VibeKB owns under `.vibekb/` and integrates with shared files (AGENTS.md, CLAUDE.md) only through namespaced adapters or a single marked managed block. New commands `migrate` and `uninstall`, a per-install manifest `.vibekb/install.json`, doctor footprint checks, and a layout-aware PHP core. Verified via go test + end-to-end runs + `check`.
updated: 2026-07-23
verification_state: verified-from-source
---

## Current state

- **Ownership model** (docs/REPOSITORY_SAFETY.md): VibeKB owns only `.vibekb/`
  plus namespaced adapters; shared files get a single `<!-- VIBEKB:START v1 -->…
  <!-- VIBEKB:END -->` managed block. Nothing at the repo root by default.
- **Payload manifest** `template/manifest.json` (schema 2) is a `src`→`dest`
  `map`: the binary embeds VibeKB's dev layout but installs it under
  `.vibekb/runtime/`, `.vibekb/reference/`, `.vibekb/prompts/`.
- **Managed-block engine** (`internal/installer/block.go`): insert/update/remove,
  idempotent, CRLF/LF and trailing-newline aware, malformed/duplicate markers →
  reported conflict (never a silent rewrite).
- **Safe install** (`installer.go`/`plan.go`/`apply.go`): flags `--dry-run`,
  `--knowledge-only`/`--no-integrations`, `--integrate <list>`, narrowed
  `--force`; atomic writes; shared-file backups under `.vibekb/backups/`;
  unrecognized `.vibekb/` treated as a collision.
- **Install manifest** `.vibekb/install.json` (`state.go`): ownership, kind,
  hashes, block version, pre-existing flag — no absolute paths.
- **migrate** (`migrate.go`) consolidates a legacy root install; **uninstall**
  (`uninstall.go`) is ownership-aware; **doctor** gained footprint diagnostics
  (`internal/installer/doctor.go`).
- **Layout-aware PHP** (`guide/lib/workspace.php`): guide/tools run from either
  the self-hosted root or `.vibekb/runtime/`; `phpcore` discovers the relocated
  `ToolsScript`. VibeKB's own self-hosted layout is unchanged.

## Verification completed

- `go build ./...` clean; `go test ./...` green (block engine, scenarios,
  manifest — empty/AGENTS/CLAUDE/both, LF/CRLF/no-final-newline, foreign
  `.vibekb`, valid/legacy installs, malformed/duplicate markers, repeat install,
  upgrade, dry-run, integrate/knowledge-only, uninstall, migrate).
- End-to-end: built `vibekb`, installed into a demo repo → root clean (only
  `.cursor`, `.git`, `.vibekb`, `AGENTS.md`, `README.md`, `src`), AGENTS.md
  managed block inserted + backed up, `php .vibekb/runtime/tools/vibekb.php status`
  / `bootstrap` / `generate` all work, guide renders; uninstall restored the repo.
- Self-hosted `php tools/vibekb.php check` OK, `php tools/test-topology.php` OK,
  `/docs` regenerated.

## Unresolved / next

- Human docs updated (README, INSTALLER, INITIALIZE, the two new `docs/` files).
  `TOKEN_ECONOMICS.md`/`ARCHITECTURE.md` were not swept for stale root-path
  language and could be reviewed later.
- No automated end-to-end test yet exercises the *installed* PHP runtime under
  `.vibekb/runtime/` from Go (covered manually); consider a scripted smoke test.
- Checksum/signing of release binaries remains a separate hardening milestone.

## Exact next recommended action

Review `TOKEN_ECONOMICS.md` and `ARCHITECTURE.md` for any remaining references to
the old root-level footprint (`guide/`, `tools/`, root `CLAUDE.md`), and update
them to the consolidated `.vibekb/` layout; then cut a 2.0.0 release.
