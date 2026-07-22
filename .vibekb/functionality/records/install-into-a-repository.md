---
id: install-into-a-repository
type: functionality
title: Install VibeKB into a repository
area: integration
summary: A PHP installer (`install.php`) that copies the VibeKB runtime into a target repository and scaffolds a fresh, empty-but-valid `.vibekb/` workspace — without touching the application's code and without analysing it — so adopting VibeKB is a command, not a manual copy.
status: implemented
verification: verified-from-source
user_facing: true
trigger: A developer clones VibeKB and runs `php install.php [target]` (or `php install.php` in the current directory).
updated: 2026-07-22
tags: [integration, installer, onboarding, scaffolding]
files: [install.php, template/manifest.json, tools/lib/Starter.php, index.php, assets/css/homepage.css, assets/js/homepage.js]
reads: [template/manifest.json]
writes: []
config: []
depends_on: [bootstrap-workspace, load-living-model]
related_memory: [decision:installer-template-not-duplicated-tree, decision:installer-prepares-agent-interprets, change:first-class-installer, change:homepage-install-fast-start]
---

## In one sentence

`php install.php /path/to/repo` prepares any repository for VibeKB — it installs
the guide, the tools, the agent instructions, and the VibeKB docs, then lays down
a fresh empty model — and then hands off to an AI agent to build the model.

## User experience

The public homepage surfaces the intended first-run path in three steps under
`#install`: clone VibeKB, run `php VibeKB/install.php /path/to/your/project`,
then ask Cursor to build the model with `prompts/INTEGRATE_VIBEKB.md`. That page
is marketing copy only — the installer itself is unchanged.

The developer runs one command and sees a plan (what will be created, replaced,
skipped, and that `.vibekb/` is a fresh model), confirms, and gets a verified
installation with explicit next steps. `--dry-run` shows the full plan and
changes nothing; `--yes` runs non-interactively; `--force` overwrites
pre-existing files (including resetting an existing model); an existing
installation is auto-detected and upgraded (runtime refreshed, `.vibekb/`
preserved).

## Current behavior

The installer resolves its own clone as the **source** and the argument (or the
current directory) as the **target**. It reads `template/manifest.json` to learn
the VibeKB-owned payload (`guide/`, `tools/`, `prompts/`, `.cursor/`, and the
VibeKB docs), refusing to install into VibeKB's own repository. It checks the
target looks like a software project, builds a per-file plan, and — after
confirmation — copies the payload, creating only missing directories and never
overwriting pre-existing files on a fresh install unless `--force` is given. It
then scaffolds a fresh `.vibekb/` via `tools/lib/Starter.php` (see **Bootstrap the
VibeKB workspace**), records installer state in `.vibekb/.installer.json`, and
verifies the result — including running the freshly installed
`php tools/vibekb.php check` against the target.

## Step-by-step flow

1. Resolve source (the VibeKB clone) and target; load `template/manifest.json`.
2. Refuse if the target is VibeKB's own repository; detect a prior install →
   upgrade mode.
3. Confirm the target looks like a project (ask if not).
4. Build and render the plan (create / replace / skip / preserve).
5. On confirmation, copy the payload; scaffold or preserve `.vibekb/`.
6. Write `.vibekb/.installer.json`; verify presence and run the target's `check`.
7. Print the next action: build the model with `prompts/INTEGRATE_VIBEKB.md`.

## Implementation map

- `install.php` — the installer: argument parsing, planning, copying, scaffolding,
  verification, and the console UI.
- `template/manifest.json` — declares the installable payload, the preserved
  project-owned paths, and the generated paths; the installer is manifest-driven,
  not a hard-coded copy list.
- `tools/lib/Starter.php` — produces the fresh workspace (shared with bootstrap).

## Data used

- **Reads:** the VibeKB source clone and `template/manifest.json`.
- **Writes:** into the target only — the payload, a fresh `.vibekb/`, and
  `.vibekb/.installer.json`. It never writes to the source and never modifies the
  target application's code.

## Dependencies

Scaffolding the fresh model is delegated to `bootstrap-workspace`
(`tools/lib/Starter.php`); the resulting model is what `load-living-model` reads.

## Failure cases

- Target missing or unwritable → reported, no partial guess.
- Pre-existing files on a fresh install → skipped for safety and reported, unless
  `--force`.
- Target is the VibeKB repo → refused, with a pointer to `bootstrap`.

## Safe to change

Adding a payload path to `template/manifest.json` extends what is installed
without touching the installer code. The console UI is presentation-only.

## Use caution

The installer must never analyse or document the target application, and must
never overwrite application code or an existing `.vibekb/` model without an
explicit `--force`. Keep the honest boundary: it prepares the workspace; the agent
interprets the software.

## Why it works this way

Making installation a real command removes the biggest adoption friction (manual
copying) while a declarative `template/manifest.json` keeps "what belongs in
another repository" explicit and upgrade-safe. The fresh model is produced
programmatically rather than copied from a second template tree, so there is no
drift-prone duplicate of the starter content.
