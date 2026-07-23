---
id: handoff
type: handoff
title: Current handoff
summary: `php tools/vibekb.php check` now watches VibeKB's own front-door docs — a new section [5] lints the root *.md for dead CLI commands, unresolved links, and a stale README .vibekb/ structure block. Verified clean on the repo and failing on injected drift. Ready for review/merge.
updated: 2026-07-23
verification_state: verified-manually
---

## Current state

- `check` has a fifth section, **[5] Documentation claims**, that lints the
  repository's root-level narrative docs (README.md, CLAUDE.md, …).
- It closes the gap a prior README audit surfaced: the drift-detection tool did
  not watch its own front door, so a renamed command, a moved file, or a stale
  structure block in the docs could drift with nothing to catch it.
- Definite doc contradictions now fail `check`; softer signals are warnings; and
  each sub-check degrades to a "skipped" note when its source of truth is absent.

## Completed work

- `tools/vibekb.php`: added `vibekb_check_docs_claims()` and helpers
  (`vibekb_known_php_subcommands`, `vibekb_known_go_commands`,
  `vibekb_fenced_blocks`, `vibekb_markdown_links`, `vibekb_parse_vibekb_tree`,
  `vibekb_vibekb_toplevel`); wired section [5] into `check`; updated the header
  docblock and `help` text.
- Model: updated `detect-drift` functionality (four → five sections, flow,
  implementation map, rationale); added change record
  `check-watches-front-door-docs`.
- Regenerated `/docs`.

## Verification

- `php tools/vibekb.php check` → RESULT OK; section [5] clean across 14 root docs.
- Negative tests (throwaway inputs): flagged a bogus `php tools/vibekb.php`
  subcommand, a bogus `vibekb` command, a broken link, and a bogus `.vibekb/`
  structure entry — each fails `check` — then confirmed clean after revert.
- Degrade path: with `internal/cli/cli.go` hidden, Go-command checks print a
  skip note and raise no false errors.
- First run caught a real bug in the link resolver (`ltrim($t, './')` mangled
  `./.github/...`); fixed to strip the `./` prefix once.
- `php -l tools/vibekb.php` clean; `php tools/vibekb.php validate` 0 errors;
  `php tools/test-topology.php` OK.

## Unresolved / next

- The lint checks the three mechanical drift classes only; it deliberately does
  not judge prose meaning (that stays an agent's job).
- Structure parity is README-only by design (INSTALLER's tree is a target-install
  layout, SCHEMA's is the schema reference). Extending parity to those would need
  per-doc "what tree is this" context — not worth the false-positive risk now.
- Pre-existing, unrelated: diagram `self-maintenance-loop` still has no
  explainable topology (validation warning).

## Exact next recommended action

Review and merge. Optionally wire `php tools/vibekb.php check` into CI (the
`.github/workflows/vibekb.yml` workflow) so the front-door lint runs on every PR.
