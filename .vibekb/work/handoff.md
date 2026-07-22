---
id: handoff
type: handoff
title: Current handoff
summary: VibeKB is now self-hosted. The active `.vibekb/` models VibeKB itself; the SousMeow example and StopPR audit are isolated under `examples/`; a new `tools/vibekb.php` CLI (status / check / affected) drives the maintenance lifecycle; agent instructions are unified and repository-owned; `/docs` is regenerated from the self-model. Next: optionally add explainable topology to the one remaining picture-only diagram (self-maintenance-loop), and keep the model reconciled as VibeKB changes.
updated: 2026-07-22
verification_state: verified-manually
---

## What the software (VibeKB) now does

VibeKB renders one repository-owned model through one template set in two modes
(dynamic guide, static `/docs`). As of this change it is **self-hosted**: the
active model describes VibeKB's own components — the loader, the guide renderer,
the static generator, the validator, the explainable-diagram system, and the new
self-maintenance CLI. Example models of other apps live under `examples/` and are
not the active model.

## Completed this change

- **Examples isolated.** SousMeow → `examples/sousmeow/.vibekb/`; StopPR audit →
  `examples/field-tests/`. `guide/index.php` gained a `VIBEKB_CONTENT_ROOT`
  override (constrained to a `.vibekb` directory) so examples stay
  previewable/validatable.
- **Self-model authored.** 17 functionality records across 7 areas, 6 system
  docs, curated important-files, memory (decisions/constraints/warnings/
  assumption/discovery/change), 3 diagrams (2 explainable), work + handoff — all
  grounded in this repository's source.
- **Self-maintenance CLI.** `tools/vibekb.php`: `status` (session start), `check`
  (validate + broken-reference detection + drift since recorded commit + `/docs`
  sync), `affected` (files → functionality), `validate`, `generate`. Honest about
  detected vs interpreted.
- **Tooling.** `tools/validate.php` accepts an optional content root; a CI
  workflow runs syntax, validation, topology, and the drift check.
- **Instructions unified.** `CLAUDE.md` canonical; `AGENTS.md` and
  `.cursor/rules/vibekb.mdc` are thin pointers. `MAINTENANCE.md`, `INITIALIZE.md`,
  `README.md`, `.cpanel.yml`, `DEPLOYMENT.md` updated for self-hosting.
- **/docs regenerated** from the self-model.

## Verification completed

- `php -l` clean across changed `guide/` and `tools/` files.
- `php tools/validate.php` → 0 errors on the root model; the SousMeow example also
  validates via the content-root argument.
- `php tools/test-topology.php` → OK.
- `php tools/vibekb.php status|check|affected` exercised; `check` reports a
  consistent model and an in-sync `/docs`.
- Dynamic guide spot-checked; `/docs` regenerated and re-validated.

## Active warnings (VibeKB)

- `model-can-drift-from-code`, `docs-is-generated-never-hand-edit`,
  `verification-must-reflect-evidence`.

## Honest limitations / not verified

- Cursor discovery is `inferred` (rules provided, not runtime-observed here).
- The live cPanel host is not exercised in this environment.
- `initialize-in-a-repository` is `partial` (documented process, no skeleton
  generator command yet).

## Exact next recommended action

If richer visuals are wanted, author explainable topology for the third diagram
(`self-maintenance-loop`) applying the same explainability gate, then re-run
`php tools/vibekb.php check` and `php tools/generate-static.php`. Otherwise, for
any future change, start with `php tools/vibekb.php status`, use `affected` to
find impacted records, update them, and finish with `check` + `generate` before
committing.
