# template/ — the VibeKB installation definition

This directory defines **what a VibeKB installation consists of**. It is read by
[`install.php`](../install.php) so the set of installed files is declared here,
explicitly and versioned — not hard-coded in the installer.

It is deliberately **not** a second copy of the runtime. VibeKB is self-hosted
(its own `.vibekb/` model describes the files at the repository root), so
duplicating `guide/`, `tools/`, etc. into `template/` would drift instantly. See
the decision record
`.vibekb/memory/decisions/installer-template-not-duplicated-tree.md`.

## Contents

- **`manifest.json`** — the declarative payload:
  - `payload` — the VibeKB-owned files the installer copies (runtime, agent
    instructions, docs). Refreshed on upgrade.
  - `preserve` — project-owned paths the installer never overwrites (`.vibekb/`).
  - `generated` — paths produced by `php tools/vibekb.php generate` (`docs/`),
    not by the installer.
  - `not_installed` — paths deliberately excluded from a target repository.
  - `starter_model` — points at `tools/lib/Starter.php`, the single source of
    truth for the fresh `.vibekb/` workspace.

## The starter model lives in code

The fresh, empty `.vibekb/` workspace is produced programmatically by
[`../tools/lib/Starter.php`](../tools/lib/Starter.php) — the same definition used
by `php tools/vibekb.php bootstrap`. That keeps the installer, bootstrap, and the
starter content perfectly consistent with no duplicated tree to maintain.

See [`../INSTALLER.md`](../INSTALLER.md) for installation, upgrades, repairs, and
extension points.
