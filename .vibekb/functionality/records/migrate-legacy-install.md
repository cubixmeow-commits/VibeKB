---
id: migrate-legacy-install
type: functionality
title: Migrate a legacy root-level install
area: integration
summary: `vibekb migrate` consolidates a pre-2.0 root-level VibeKB install under `.vibekb/`. It installs the consolidated layout, converts a whole-file VibeKB CLAUDE.md/AGENTS.md into a managed block only when the exact first-line title plus supporting signatures match (preserving user-authored and lookalike content), relocates the reference docs, and removes only root-level files it can positively identify as unmodified VibeKB content by byte-for-byte hash. Modified or ambiguous files are preserved and reported; backups are written under `.vibekb/backups/`.
status: implemented
verification: verified-from-source
user_facing: true
trigger: A developer runs `vibekb migrate [target]` (auto-suggested when `install` detects a legacy layout).
updated: 2026-07-23
tags: [integration, migration, repository-safety, managed-block, consolidation]
files: [internal/installer/migrate.go, internal/installer/block.go, internal/installer/plan.go, internal/installer/apply.go, internal/cli/cli.go]
reads: [template/manifest.json, template/integrations, template/starter]
writes: []
config: []
depends_on: [install-into-a-repository]
related_memory: [decision:repository-safety-consolidation]
---

## In one sentence

`vibekb migrate` moves an old root-level VibeKB footprint into `.vibekb/` without
losing anything, converting shared docs to managed blocks and removing only files
it can prove are unmodified VibeKB content.

## Current behavior

Migration runs in a safe order:

1. **Convert legacy shared docs first.** If `CLAUDE.md`/`AGENTS.md` has the exact
   VibeKB whole-file title as its first non-empty line *and* all supporting
   signatures, and has no managed block, it is backed up and replaced by a
   managed block. A user-authored or lookalike file (wrong title, even if it
   quotes VibeKB phrases) is left in place; a managed block is added later by the
   consolidation step, preserving its text.
2. **Install the consolidated layout** under `.vibekb/` (payload + adapters +
   model preserve/scaffold), writing `.vibekb/install.json`.
3. **Relocate reference docs.** Root `PRODUCT.md`, `SCHEMA.md`, `INITIALIZE.md`,
   `MAINTENANCE.md`, `INSTALLER.md` that are byte-identical to VibeKB's embedded
   copies are backed up and removed (the canonical copy is now under
   `.vibekb/reference/`); modified copies are preserved and reported.
4. **Remove unmodified root runtime.** `guide/`, `tools/`, `prompts/`,
   `template/starter/` are removed only when every file matches the embedded
   payload; a directory with any modified or unrecognized file is left in place.
5. **Drop the legacy state** `.vibekb/.installer.json` (replaced by
   `install.json`).

`--dry-run` reports every action without changing anything. Migration is
repeatable.

## Implementation map

- `internal/installer/migrate.go` — detection, ordering, hash/signature checks,
  and the report.
- `internal/installer/block.go` — rendering the managed block and detecting
  existing markers.
- `internal/installer/plan.go` / `apply.go` — the consolidated install it reuses.

## Failure cases

- A modified legacy file → preserved and reported, never deleted.
- Malformed markers in a shared file → left untouched.
- Target is VibeKB's own self-hosted repo → refused.

## Use caution

Migration must never delete a file on filename alone — only content-verified,
unmodified VibeKB files are removed. Everything else is preserved with a report.
