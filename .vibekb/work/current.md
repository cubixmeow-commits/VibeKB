---
id: current-work
type: work
title: Bootstrap VibeKB on itself and make self-maintenance real
objective: Replace the SousMeow active model with VibeKB's own living model, isolate the examples, and add a repository-owned maintenance lifecycle (a `vibekb` CLI plus unified agent instructions) so VibeKB maintains its own model as it is developed.
summary: Self-hosting VibeKB — the active `.vibekb/` now describes VibeKB, examples are isolated under `examples/`, and `tools/vibekb.php` gives agents status / check / affected for the lifecycle.
requested_by: Project owner
status: completed
verification_state: verified-by-test
updated: 2026-07-22
affected_functionality: [start-work-session, detect-drift, find-affected-functionality, load-living-model, validate-model, generate-static-snapshot, render-guide]
expected_files:
  - tools/vibekb.php
  - tools/validate.php
  - guide/index.php
  - .vibekb/ (the whole active self-model)
  - examples/sousmeow/.vibekb/
  - examples/field-tests/STOPPR_INTEGRATION_AUDIT.md
  - CLAUDE.md, AGENTS.md, .cursor/rules/vibekb.mdc
  - MAINTENANCE.md, INITIALIZE.md, README.md, .cpanel.yml, DEPLOYMENT.md
  - .github/workflows/vibekb.yml
  - docs/ (regenerated)
data_impact: No runtime data. Repository-owned content and product code only. The SousMeow example is unchanged in content, only relocated.
risks:
  - Making the static snapshot the centre of the architecture (kept it a projection).
  - Claiming automatic semantic updates (kept detection and interpretation separate).
  - Letting the example model be mistaken for the active model (isolated under examples/).
  - Over-building tooling into a checklist agents ignore (kept to one discoverable CLI).
  - Verification overreach on the new CLI records (gated on actually testing the CLI).
---

## What the user asked for

Bootstrap VibeKB inside its own repository and make self-maintenance a real part
of how the repository operates — an active model of VibeKB itself, examples
cleanly separated, a repository-owned lifecycle that Claude Code and Cursor
follow without a giant prompt, mechanical drift detection that is honest about the
detection-vs-interpretation boundary, and a `/docs` site that demonstrates VibeKB
explaining VibeKB.

## Current behaviour (before)

The active `.vibekb/` modelled SousMeow. VibeKB had the renderer, validator, and
explainable diagrams, but no self-model and no lifecycle tooling; maintenance was
prose in CLAUDE.md / MAINTENANCE.md.

## Proposed behaviour (after)

- Root `.vibekb/` is VibeKB's own model; SousMeow and the StopPR audit move under
  `examples/`, still previewable via `VIBEKB_CONTENT_ROOT`.
- `tools/vibekb.php` provides `status`, `check`, `affected`, `validate`,
  `generate`.
- `CLAUDE.md` is the canonical workflow; `AGENTS.md` and `.cursor/rules` are thin
  pointers. `tools/validate.php` takes an optional content root; CI runs the
  checks.
- `/docs` is regenerated from the self-model.

## Verification plan

`php -l` across changed PHP; `php tools/validate.php` on the root model and the
example; `php tools/test-topology.php`; `php tools/vibekb.php status|check|affected`
exercised; `/docs` regenerated and re-validated; guide views spot-checked in both
modes.
