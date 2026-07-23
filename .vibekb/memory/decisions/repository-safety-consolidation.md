---
id: repository-safety-consolidation
type: decision
title: VibeKB owns only .vibekb/; everything else is an adapter or managed block
summary: Installation consolidates the entire VibeKB-owned footprint under `.vibekb/` (runtime, reference, prompts, model). Files outside it are optional adapters — namespaced files VibeKB owns inside a shared tool directory, or a single clearly marked managed block inside a shared, user-owned file (AGENTS.md, CLAUDE.md). VibeKB never owns generic root files, and the payload manifest remaps embedded source paths to `.vibekb/`-relative destinations on install.
status: accepted
verification: verified-from-source
updated: 2026-07-23
functionality: [install-into-a-repository, migrate-legacy-install, uninstall-from-a-repository, run-the-developer-cli]
files: [template/manifest.json, internal/installer/plan.go, internal/installer/block.go, internal/installer/apply.go, internal/installer/state.go, guide/lib/workspace.php]
tags: [installer, repository-safety, ownership, managed-block, namespaced-adapter, migration, consolidation]
---

## Context

The pre-2.0 installer wrote a large footprint into the **target repository root**:
seven Markdown files (including `CLAUDE.md` and `AGENTS.md`, which are owned by
the user and by coding agents), plus `guide/`, `tools/`, `template/`, `prompts/`,
and `.cursor/`. It could create `CLAUDE.md`/`AGENTS.md` where none existed and
overwrite them under `--force`. Users could reasonably fear running
`vibekb install .` in an existing project — the audit is recorded in
`docs/REPOSITORY_FOOTPRINT_AUDIT.md`.

## Decision

Establish a three-category ownership model (see `docs/REPOSITORY_SAFETY.md`):

- **VibeKB-owned** — everything under `.vibekb/`, plus namespaced adapters
  (`.cursor/rules/vibekb.mdc`, `.github/instructions/vibekb.instructions.md`).
  Safe to create, refresh, and remove.
- **Shared** — `AGENTS.md`, `CLAUDE.md`, and similar. VibeKB edits only a single
  marked managed block (`<!-- VIBEKB:START v1 --> … <!-- VIBEKB:END -->`),
  preserving everything else byte-for-byte, idempotent, line-ending aware, with a
  backup before the first edit. Malformed/duplicate markers are a reported
  conflict, never a silent rewrite.
- **Repository-owned** — never modified automatically.

The payload manifest (`template/manifest.json`, schema 2) becomes a `map` of
embedded `src` → target `dest`, so the binary still embeds VibeKB's own dev
layout (`guide/`, `tools/`) but installs it under `.vibekb/runtime/`. The PHP core
is made layout-aware (`guide/lib/workspace.php`) so it runs from either the
self-hosted root layout or the consolidated `.vibekb/runtime/` layout.

Default install favors safety: `.vibekb/` always; namespaced adapters only when
that tool is already in use; a managed block in `AGENTS.md`/`CLAUDE.md` only if
the file already exists. Flags: `--knowledge-only`/`--no-integrations`,
`--integrate <list>`, `--dry-run`, a narrowed `--force`. A per-install manifest
(`.vibekb/install.json`) records ownership, kind, hashes, and provenance so
`migrate`, `doctor`, and `uninstall` are safe and repeatable.

## Consequences

- `vibekb install .` is repository-safe and idempotent; the root is untouched by
  default.
- New commands: `vibekb migrate` (consolidate a legacy root install) and
  `vibekb uninstall` (ownership-aware removal). `vibekb doctor` gains footprint
  diagnostics.
- The installed runtime path moved to `.vibekb/runtime/tools/vibekb.php`; the Go
  CLI discovers it, and `phpcore` prefers it over the legacy `tools/vibekb.php`.
- VibeKB's own self-hosted repo keeps its root layout (marketing site, GitHub
  Pages `/docs`, Go embed source) — the remap applies only to target repos.
