---
id: first-class-installer
type: change
title: Add a first-class installer and bootstrap command
summary: Replaced the manual "copy these files" onboarding with `php install.php`, a bootstrap command, and a shared starter library, so adopting VibeKB into a repository is one command that prepares the workspace and hands off to an agent.
status: implemented
verification: verified-from-source
updated: 2026-07-22
functionality: [install-into-a-repository, bootstrap-workspace, initialize-in-a-repository]
files: [install.php, template/manifest.json, tools/lib/Starter.php, tools/vibekb.php]
tags: [installer, onboarding, cli, change]
---

## Before

Adopting VibeKB meant manually copying `guide/`, `tools/`, `PRODUCT.md`,
`CLAUDE.md`/`AGENTS.md`, and `SCHEMA.md` into the target repository and then
authoring a `.vibekb/` model by hand, following `INITIALIZE.md`. There was no
scaffolding command; the `initialize-in-a-repository` record explicitly noted the
gap.

## After

- `install.php` — a PHP 8.2 installer (no Composer, no network, cross-platform):
  detects the target, shows a create/replace/skip/preserve plan, copies the
  VibeKB-owned payload declared in `template/manifest.json`, scaffolds a fresh
  empty model, records installer state in `.vibekb/.installer.json`, and verifies
  the result (including the target's own `vibekb check`). Supports `--dry-run`,
  `--yes`, `--force`, and auto-detected upgrades that preserve `.vibekb/`.
- `php tools/vibekb.php bootstrap` — verifies and repairs a workspace ("git init
  for VibeKB"), never overwriting content.
- `tools/lib/Starter.php` — the single definition of the starter directories and
  files, shared by the installer and bootstrap.
- `template/` — a declarative payload manifest (not a duplicated file tree).

## Impact

Onboarding goes from a documented manual copy to one command. The honest boundary
is preserved: the installer prepares the workspace and an agent still builds the
model. `initialize-in-a-repository` was updated to depend on the installer, and
`template/` is excluded from VibeKB's drift detection.
