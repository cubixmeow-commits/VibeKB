---
id: install-into-a-repository
type: functionality
title: Install VibeKB into a repository
area: integration
summary: A fully native, repository-safe installer built into the `vibekb` Go binary. `vibekb install /path/to/repo` consolidates everything VibeKB owns under the target's `.vibekb/` (runtime, reference docs, prompts, and a fresh empty model) and integrates with shared files (AGENTS.md, CLAUDE.md) only through namespaced adapters or a single clearly marked managed block — nothing is written at the repository root by default. It runs from the binary's embedded payload without executing PHP; PHP is required only to run the installed guide.
status: implemented
verification: verified-from-source
user_facing: true
trigger: A developer runs `vibekb install [target]` (or the legacy `php install.php`, which now forwards to it).
updated: 2026-07-23
tags: [integration, installer, onboarding, scaffolding, go, embed, native, repository-safety, managed-block]
files: [cmd/vibekb/main.go, internal/installer/installer.go, internal/installer/plan.go, internal/installer/apply.go, internal/installer/block.go, internal/installer/manifest.go, internal/installer/state.go, internal/installer/scaffold.go, internal/installer/console.go, embed.go, template/manifest.json, template/starter/starter.json, template/integrations/agents-block.md, tools/lib/Starter.php, install.php]
reads: [template/manifest.json, template/starter, template/integrations]
writes: []
config: []
depends_on: [bootstrap-workspace, load-living-model, run-the-developer-cli]
related_memory: [decision:repository-safety-consolidation, decision:native-installer-embedded-payload, decision:installer-template-not-duplicated-tree, decision:installer-prepares-agent-interprets, change:native-go-installer, change:website-curl-installer]
---

## In one sentence

`vibekb install /path/to/repo` prepares any repository for VibeKB by writing
everything VibeKB owns **under `.vibekb/`** — the guide, the tools, the reference
docs, the integration prompt, and a fresh empty model — from the binary's
embedded payload, integrating with shared files only through namespaced adapters
or a marked managed block, and never touching the repository root by default.

## User experience

A developer installs the CLI via the website
(`curl -fsSL https://iainreid.dev/vibekb/install.sh | sh`) — or builds with
`go build -o vibekb ./cmd/vibekb` — and runs `vibekb install
/path/to/your/project`. They see a plan (what VibeKB-owned files go under
`.vibekb/`, whether the model is created fresh or preserved, and which optional
adapters apply), confirm, and get a verified installation with explicit next
steps. Controls:

- `--dry-run` shows every proposed change (including managed-block
  insert/update/conflict previews) and writes nothing.
- `--knowledge-only` / `--no-integrations` install only `.vibekb/`.
- `--integrate cursor,claude,agents` installs only the named adapters.
- `--force` is narrowed: it only permits taking over an *unrecognized* `.vibekb/`
  and resetting the model; it never overwrites shared files wholesale or writes
  outside `.vibekb/` and the declared adapters.
- A prior install is auto-detected and upgraded (runtime refreshed, model
  preserved).

The historical `php install.php` entry point still works as a thin wrapper.

## Current behavior

The installer is native Go. It reads the embedded `template/manifest.json`
(schema 2), whose `payload.map` maps each embedded source (`guide`, `tools`,
`template/starter`, the reference docs, the prompt) to a destination **under
`.vibekb/`** (e.g. `guide` → `.vibekb/runtime/guide`). It refuses to install into
VibeKB's own self-hosted repo, and refuses to write into a `.vibekb/` it does not
recognize (a namespaced collision) unless `--force` is given. It builds a plan
(payload + selected adapters + fresh/preserve model), and — after confirmation —
applies it transaction-style: payload and namespaced files are written atomically
(temp + rename); shared files are backed up under `.vibekb/backups/` before a
managed block is inserted or updated; malformed/duplicate markers are reported as
a conflict and skipped. It scaffolds a fresh model directly under `.vibekb/` (or
preserves an existing one), writes the installation manifest `.vibekb/install.json`
last (ownership, kind, hashes, provenance — no absolute paths), and verifies the
result natively.

Adapter selection favors safety: namespaced adapters (`.cursor/rules/vibekb.mdc`,
`.github/instructions/vibekb.instructions.md`) apply only when that tool is
already in use; the `AGENTS.md`/`CLAUDE.md` managed block applies only if the
file already exists — VibeKB never creates those files by default.

## Step-by-step flow

1. Parse the embedded manifest; resolve the target.
2. Refuse VibeKB's own self-hosted repo; refuse an unrecognized `.vibekb/`
   without `--force`; detect a prior install → upgrade mode; detect a legacy
   root-level install → suggest `vibekb migrate`.
3. Confirm the target looks like a project (ask if not).
4. Build and render the plan (payload under `.vibekb/`, model create/preserve,
   optional adapters with managed-block previews).
5. On confirmation, apply atomically: write payload + namespaced adapters; back
   up and edit shared files' managed blocks; scaffold or preserve the model.
6. Write `.vibekb/install.json`; verify presence natively.
7. Print the next action: build the model with
   `.vibekb/prompts/INTEGRATE_VIBEKB.md` (and note that `vibekb check` / the guide
   need PHP 8.2+).

## Implementation map

- `cmd/vibekb/main.go` → `internal/cli` dispatch → `internal/installer`.
- `internal/installer/installer.go` — orchestration, options/flags, rendering,
  verification, next steps.
- `internal/installer/plan.go` — recognition, adapter selection, and the plan
  (payload remap + integrations).
- `internal/installer/apply.go` — atomic writes, shared-file backups, applying
  managed blocks, recording file ownership.
- `internal/installer/block.go` — the managed-block engine (insert/update/remove,
  idempotent, line-ending and trailing-newline aware, conflict detection).
- `internal/installer/manifest.go` / `state.go` — the payload manifest parser and
  the `.vibekb/install.json` installation manifest.
- `internal/installer/scaffold.go` — fresh-model scaffolding + embedded-FS helpers.
- `embed.go` — embeds the payload, manifest, starter, and integration templates.
- `template/manifest.json` — the `src`→`dest` payload map, adapters, block
  markers, and ownership rules (single source of truth).
- `guide/lib/workspace.php` — layout-aware content-root resolution so the PHP core
  runs from `.vibekb/runtime/`.

## Data used

- **Reads:** the binary's embedded payload; at install time, the target directory
  to plan, back up, and verify.
- **Writes:** into the target only — under `.vibekb/`, plus namespaced adapters
  and (if applicable) a managed block in an existing shared file, and backups
  under `.vibekb/backups/`. It never modifies application code and never launches
  PHP.

## Dependencies

Scaffolding the fresh model uses the same starter definition as
`bootstrap-workspace`; the resulting model is what `load-living-model` reads. The
installer is one subcommand of `run-the-developer-cli`, alongside `migrate` and
`uninstall`.

## Failure cases

- Target missing or unwritable → reported, no partial guess.
- Unrecognized `.vibekb/` present → refused without `--force`.
- Malformed/duplicated managed markers in a shared file → integration skipped and
  reported; the file is left untouched.
- Target is VibeKB's own self-hosted repo → refused.
- Corrupt embedded manifest → reported clearly.

## Safe to change

Adding a payload entry to `template/manifest.json` `map` extends what is installed
— provided the same `src` is embedded in `embed.go`. Adapters and their default
conditions are declared in the manifest, not hard-coded. The console UI is
presentation-only.

## Use caution

The installer must never analyse or document the target application, never own or
overwrite a generic repository file, and never edit a shared file outside its
managed block. Keep the honest boundary: it prepares the workspace; the agent
interprets the software. Keep the payload embedded and keep everything VibeKB owns
under `.vibekb/`.

## Why it works this way

Consolidating under `.vibekb/` and integrating only through namespaced files or a
marked managed block means a user is never afraid to run `vibekb install .` in an
existing project — VibeKB cannot clobber `CLAUDE.md`, `AGENTS.md`, `README`, or
their `docs/`. The manifest stays the single source of truth, and the same starter
definition feeds both the Go installer and PHP `bootstrap`, so nothing drifts.
