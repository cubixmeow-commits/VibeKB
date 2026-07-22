---
id: handoff
type: handoff
title: Current handoff
summary: The homepage now leads with a three-step install fast-start (clone → install.php → Cursor prompt) under the hero, matching the real installer. Next: keep the model reconciled as VibeKB changes.
updated: 2026-07-22
verification_state: verified-from-source
---

## Completed this change

- Homepage `#install` moved directly under the hero (before “What you get”).
- Three primary steps with working copy buttons: clone command, install command
  (+ example), and a Cursor prompt pointing at `prompts/INTEGRATE_VIBEKB.md`.
- Result strip distinguishes installed runtime from `docs/` (generated later).
- Expandable “What does the installer do?” disclosure with payload list and
  optional `--dry-run`.
- Nav order: The problem → Install → What you get → See it work.
- Model reconciled: important-files for homepage assets, new change record
  `homepage-install-fast-start`, installer/initialize records linked, provenance
  and work records updated.

## Verification completed

- Displayed commands checked against `install.php` / `INSTALLER.md` /
  `template/manifest.json` (no fabricated commands).
- `php -l index.php`, `php -l install.php`.
- Rendered `index.php`: section order problem → install → understanding → proof;
  GitHub + INSTALLER.md links present; honesty phrases present; no auto-understand
  claims.
- `php tools/vibekb.php check`, `php tools/test-topology.php`,
  `php tools/vibekb.php generate` (run before commit).

## Unresolved / active warnings

None introduced by this change. Homepage copy-button click behaviour was not
exercised in a real browser in this environment (Clipboard API) — the JS path
and no-JS readability were reviewed from source; treat interactive copy as
`inferred-from-source` until manually clicked.

## Exact next recommended action

`php tools/vibekb.php status` before the next change. If install commands or the
integration prompt change, update the homepage `#install` section in the same
commit as `INSTALLER.md` / `install.php` / `prompts/INTEGRATE_VIBEKB.md`.
