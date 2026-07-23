---
id: uninstall-from-a-repository
type: functionality
title: Uninstall VibeKB from a repository
area: integration
summary: `vibekb uninstall` removes VibeKB from a repository, ownership-aware. Driven by the `.vibekb/install.json` manifest, it deletes VibeKB-owned files (everything under `.vibekb/` and namespaced adapters) and strips only VibeKB's managed block from shared files (AGENTS.md, CLAUDE.md), preserving everything else. Shared-file backups are retained under `--keep-knowledge` or relocated to a stamped temp directory on a full uninstall so they are not lost with `.vibekb/`.
status: implemented
verification: verified-from-source
user_facing: true
trigger: A developer runs `vibekb uninstall [target]`.
updated: 2026-07-23
tags: [integration, uninstall, repository-safety, managed-block, ownership]
files: [internal/installer/uninstall.go, internal/installer/block.go, internal/installer/state.go, internal/cli/cli.go]
reads: [.vibekb/install.json]
writes: []
config: []
depends_on: [install-into-a-repository]
related_memory: [decision:repository-safety-consolidation]
---

## In one sentence

`vibekb uninstall` reverses an installation safely: it removes what VibeKB owns
and strips only its managed block from shared files, leaving all of your own
content intact.

## Current behavior

Uninstall reads `.vibekb/install.json`. For each recorded shared file it removes
only the managed block (via the block engine), backing the file up under
`.vibekb/backups/` first; a shared file VibeKB created solely to hold its block is
removed, while a file that pre-existed VibeKB is kept. Namespaced adapters are
removed (and now-empty parent directories tidied), unless the manifest marks them
pre-existing.

With `--keep-knowledge`, it removes only `runtime/`, `reference/`, `prompts/`,
`generated/`, and `install.json`, retaining the model records **and**
`.vibekb/backups/`. On a full uninstall it relocates any `.vibekb/backups/`
contents to a stamped directory under the system temp folder (and prints that
path) before deleting `.vibekb/`, so shared-file snapshots survive. Files with
malformed markers are left untouched and reported. `--dry-run` previews every
action.

Without a `.vibekb/install.json` manifest, uninstall refuses to strip managed
blocks (it cannot prove ownership) and reports that a legacy install should be
migrated first.

## Implementation map

- `internal/installer/uninstall.go` — the manifest-driven removal, backup
  relocation, flags, and report.
- `internal/installer/block.go` — `removeManagedBlock` (preserves outside content,
  refuses malformed markers).
- `internal/cli/cli.go` — dispatches `uninstall` to `installer.Uninstall`.

## Failure cases

- No install manifest → nothing changed; suggests `vibekb migrate`.
- Shared file with malformed markers → skipped and reported.
- Target is VibeKB's own self-hosted repo → refused.

## Use caution

Uninstall must never delete a shared file's non-VibeKB content, and never remove a
file it cannot prove VibeKB created. Ambiguity is preserved, not resolved.
Shared-file backups must outlive a full uninstall.
