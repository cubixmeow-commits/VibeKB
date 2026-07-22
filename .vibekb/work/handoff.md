---
id: handoff
type: handoff
title: Current handoff
summary: Added TOKEN_ECONOMICS.md — an evidence-based evaluation of VibeKB's token cost/benefit with an empirical cost model, five scenarios, and a break-even framework — and a "Context economy" rule in CLAUDE.md enforcing selective retrieval (never load docs/ or examples/ into working context). Analysis only; no runtime/product behaviour change. Next: keep the model reconciled as VibeKB changes; optionally promote the measurement script to a `tools/vibekb.php context` command.
updated: 2026-07-22
verification_state: verified-manually
---

## What the software (VibeKB) now does

Runtime behaviour is unchanged — this was an analysis pass plus a documentation
guardrail. Two prior homepage changes remain in place. The new material:
`TOKEN_ECONOMICS.md` at the repo root evaluates whether maintaining/using VibeKB
is token-justified, and CLAUDE.md gained a "Context economy" section directing
agents to orient cheaply (`status` ~400 tok + CLAUDE.md), retrieve by affected
scope, let the deterministic CLI do mechanical work, and never load the generated
`docs/` or `examples/` trees into context.

## Completed this change

- `TOKEN_ECONOMICS.md`: executive conclusion, implementation analysis, measured
  context (whole-repo/by-kind/by-load-behaviour/per-file), baseline comparison,
  five scenarios, a break-even model (`N* = (B + C·M)/S`), risk-adjusted value,
  ranked optimizations, recommended operating model, go/no-go, and a
  reproducible measurement appendix.
- `CLAUDE.md`: new "Context economy (keep VibeKB token-positive)" section after
  the session-start orientation line; links to TOKEN_ECONOMICS.md.

## Key measured findings (chars/4 ≈ tokens, approximate)

- Total tracked ~402K tok. Generated `docs/` ~206K (51%), `examples/` ~56.6K
  (14%) → ~264K (65%) that should NEVER enter agent context.
- Active `.vibekb/` source ~35.5K tok across 56 files; honest session-start floor
  ~2K (CLAUDE.md + `status` output), not the full ~5.7K "always" set.
- `status`/`check`/`affected`/`validate`/`generate` are deterministic PHP — 0 LLM
  tokens; agent pays only to read compact output (~220–430 tok) and write updates.
- Anchor: understanding drift-detection from source ~22K tok vs the detect-drift
  record ~1K tok = ~20x compression.
- Break-even ≈ 6–15 fresh sessions/handoffs for this medium-small repo;
  token-negative for one-offs and single-session same-context work.

## Verification completed

- Measurement reproduced deterministically over `git ls-files`; command-output
  sizes measured by byte count; subsystem anchor measured from real file sizes.
- `php tools/vibekb.php check` and `php tools/test-topology.php` run before finish;
  `/docs` regenerated. (CLAUDE.md and TOKEN_ECONOMICS.md are not rendered into
  `/docs`, so the snapshot is unaffected by them.)

## Active warnings (VibeKB)

- `model-can-drift-from-code`, `docs-is-generated-never-hand-edit`,
  `verification-must-reflect-evidence`.

## Honest limitations / not verified

- Token figures use the chars/4 approximation, not a real tokenizer; exploration
  and per-change costs are modelled estimates, not A/B telemetry. The report
  labels these throughout and lists them as evidence still needed.
- The measurement script remains in the session scratchpad; it was intentionally
  not promoted to permanent tooling (recommended as future work #3).

## Exact next recommended action

For any future change, start with `php tools/vibekb.php status`, use `affected`
to find impacted records, update them, and finish with `check` + `generate`
before committing. If pursuing the economics work: implement
`php tools/vibekb.php context` (optimization #3) with its own functionality
record, reusing the appendix methodology in TOKEN_ECONOMICS.md.
