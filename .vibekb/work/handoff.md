---
id: handoff
type: handoff
title: Current handoff
summary: VibeKB now has a first-class installer (`php install.php`) plus a `vibekb bootstrap` command and a shared starter library, so adopting VibeKB into another repository is one command that prepares the workspace and hands off to an agent. Next: keep the model reconciled as VibeKB changes.
updated: 2026-07-22
verification_state: verified-from-source
---

## Completed this change

- New `install.php` — the VibeKB installer. Detects the target, reads
  `template/manifest.json`, shows a create/replace/skip/preserve plan, copies the
  VibeKB-owned payload, scaffolds a fresh empty `.vibekb/`, records installer
  state in `.vibekb/.installer.json`, and verifies (including the target's own
  `vibekb check`). Supports `--dry-run`, `--yes`, `--force`, and auto-detected
  upgrades that preserve `.vibekb/`. Refuses to install into VibeKB's own repo.
- New `tools/lib/Starter.php` — the single source of truth for the starter
  workspace (required dirs + starter files + verify/scaffold helpers), shared by
  the installer and bootstrap. Writes only empty placeholders — never
  functionality about the target.
- New `bootstrap` command in `tools/vibekb.php` — verifies and repairs a
  workspace ("git init for VibeKB"), never overwriting content. `template/` added
  to the drift-exclusion set.
- New `template/manifest.json` — declarative payload definition (not a duplicated
  file tree).
- Model reconciled: two new functionality records (`install-into-a-repository`,
  `bootstrap-workspace`) + index order; `initialize-in-a-repository` updated to
  depend on the installer; three memory records (two decisions, one change);
  three new important-files entries and the `tools/vibekb.php` entry updated;
  manifest provenance/scope updated.
- Docs: new `INSTALLER.md`; `README.md`, `INITIALIZE.md`, `MAINTENANCE.md`, and
  the homepage updated to lead with the installer; `.cpanel.yml` + `DEPLOYMENT.md`
  updated to exclude `install.php`, `template/`, and `tools/lib/`.

## Verification completed

- `php -l` on `install.php`, `tools/lib/Starter.php`, `tools/vibekb.php`.
- End-to-end: `install.php --dry-run` and a real `--yes` install into a scratch
  git repo (42 runtime files + 19 dirs + 17 starter files); the target's
  `php tools/vibekb.php check` reports OK. An upgrade run refreshed the runtime
  and preserved a hand-edited `.vibekb/`. `bootstrap` recreated deleted dirs and
  files and kept existing content. The scaffolded model passes
  `php tools/validate.php <root>` with 0 errors.
- `php tools/vibekb.php check` — 0 errors; `php tools/test-topology.php` OK;
  `/docs` regenerated.

## Exact next recommended action

`php tools/vibekb.php status` before the next change; `affected` → update model →
`check` + `generate` before commit. If the installer payload changes, update
`template/manifest.json` and `INSTALLER.md` together.
