---
id: install-into-a-repository
type: functionality
title: Install VibeKB into a repository
area: integration
summary: A fully native installer built into the `vibekb` Go binary. `vibekb install /path/to/repo` copies the VibeKB runtime and scaffolds a fresh, empty-but-valid `.vibekb/` workspace from files embedded in the binary — without executing PHP, without analysing the application, and without needing the source repository to remain on disk. PHP is required only to run the installed guide.
status: implemented
verification: verified-from-source
user_facing: true
trigger: A developer runs `vibekb install [target]` (or the legacy `php install.php`, which now forwards to it).
updated: 2026-07-23
tags: [integration, installer, onboarding, scaffolding, go, embed, native]
files: [cmd/vibekb/main.go, internal/installer/installer.go, internal/installer/console.go, embed.go, template/manifest.json, template/starter/starter.json, tools/lib/Starter.php, install.php]
reads: [template/manifest.json, template/starter]
writes: []
config: []
depends_on: [bootstrap-workspace, load-living-model, run-the-developer-cli]
related_memory: [decision:native-installer-embedded-payload, decision:installer-template-not-duplicated-tree, decision:installer-prepares-agent-interprets, change:native-go-installer, change:homepage-native-installer-copy]
---

## In one sentence

`vibekb install /path/to/repo` prepares any repository for VibeKB — it copies the
guide, the tools, the agent instructions, the VibeKB docs, and the starter
definition, then lays down a fresh empty model — all from the binary's embedded
payload, with no PHP process launched, and hands off to an AI agent to build the
model.

## User experience

A developer builds the binary once (`go build -o vibekb ./cmd/vibekb`) — or, in
future, installs it via brew/winget/curl — and runs `vibekb install
/path/to/your/project`. They see a plan (what will be created, replaced, skipped,
and that `.vibekb/` is a fresh model), confirm, and get a verified installation
with explicit next steps. `--dry-run` shows the full plan and changes nothing;
`--yes` runs non-interactively; `--force` overwrites pre-existing files (including
resetting an existing model); an existing installation is auto-detected and
upgraded (runtime refreshed, `.vibekb/` preserved).

The historical `php install.php` entry point still works: it is now a thin
compatibility wrapper that forwards to `vibekb install`, or prints how to get the
binary if none is found.

## Current behavior

The installer is native Go. It reads the embedded `template/manifest.json` to
learn the VibeKB-owned payload (`guide/`, `tools/`, `prompts/`, `.cursor/`, the
VibeKB docs, and `template/starter/`), refuses to install into VibeKB's own
self-hosted repository (detected via `.vibekb/manifest.json` `self_hosted: true`),
checks the target looks like a software project, builds a per-file plan, and —
after confirmation — copies the payload from the embedded filesystem, creating
only missing directories and never overwriting pre-existing files on a fresh
install unless `--force` is given. It then scaffolds a fresh `.vibekb/` from the
embedded `template/starter/` definition (see **Bootstrap the VibeKB workspace**),
records installer state in `.vibekb/.installer.json`, and verifies the result
**natively** — presence checks plus a workspace-completeness check against the
embedded starter definition. It never runs `php` to install or verify.

## Step-by-step flow

1. Parse the embedded `template/manifest.json`; resolve the target.
2. Refuse if the target is VibeKB's own self-hosted repository; detect a prior
   install → upgrade mode.
3. Confirm the target looks like a project (ask if not).
4. Build and render the plan (create / replace / skip / preserve).
5. On confirmation, copy the payload from the embedded FS; scaffold or preserve
   `.vibekb/` from the embedded starter definition.
6. Write `.vibekb/.installer.json`; verify presence and workspace completeness
   natively.
7. Print the next action: build the model with `prompts/INTEGRATE_VIBEKB.md`
   (and note that `vibekb check` / the guide need PHP 8.2+).

## Implementation map

- `cmd/vibekb/main.go` → `internal/cli` dispatch → `internal/installer`.
- `internal/installer/installer.go` — manifest parsing, planning, embedded-FS
  copying, native scaffolding and verification, installer state.
- `internal/installer/console.go` — the plan/confirm/report console UI.
- `embed.go` — embeds the payload, the manifest, and `template/starter/` into the
  binary (the embed directive must live at the module root).
- `template/manifest.json` — declares the installable payload and preserved
  paths; parsed directly by Go (no second manifest format).
- `template/starter/` — the canonical, language-neutral starter definition.
- `tools/lib/Starter.php` — reads the *same* `template/starter/` data for
  `bootstrap` (shared definition, no duplication).
- `install.php` — a compatibility wrapper that forwards to `vibekb install`.

## Data used

- **Reads:** the binary's embedded payload (manifest + runtime + starter). At
  install time it reads only the target directory to plan and verify.
- **Writes:** into the target only — the payload, a fresh `.vibekb/`, and
  `.vibekb/.installer.json`. It never modifies the target application's code and
  never launches PHP.

## Dependencies

Scaffolding the fresh model uses the same starter definition as
`bootstrap-workspace`; the resulting model is what `load-living-model` reads. The
installer is one subcommand of `run-the-developer-cli`.

## Failure cases

- Target missing or unwritable → reported, no partial guess.
- Pre-existing files on a fresh install → skipped for safety and reported, unless
  `--force`.
- Target is VibeKB's own self-hosted repo → refused, with a pointer to
  `bootstrap`.
- Corrupt embedded manifest → reported clearly (the binary was built wrong).

## Safe to change

Adding a payload path to `template/manifest.json` extends what is installed —
provided the same path is embedded in `embed.go` so its bytes are available. The
console UI is presentation-only.

## Use caution

The installer must never analyse or document the target application, and must
never overwrite application code or an existing `.vibekb/` model without an
explicit `--force`. Keep the honest boundary: it prepares the workspace; the agent
interprets the software. And keep the payload embedded — installation must not
depend on PHP or on the source repository still being present.

## Why it works this way

Embedding the payload makes installation a single self-contained binary step, so
"install VibeKB" no longer requires a PHP runtime or a live clone — the entry
point becomes uniform and packageable (brew/winget/curl). The manifest stays the
single source of truth for what is installed, and the starter model is one
canonical data definition read by both the Go installer and PHP `bootstrap`, so
nothing is duplicated or drift-prone.
