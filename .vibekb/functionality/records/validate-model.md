---
id: validate-model
type: functionality
title: Validate the model
area: living-model
summary: The loader and the headless `tools/validate.php` enforce the content contract — required fields, controlled vocabularies, resolvable references, well-formed diagram assets, provenance completeness, and reconciled totals — reporting structural errors that gate generation and CI.
status: implemented
verification: verified-from-source
user_facing: true
trigger: The loader validates on every load; `php tools/validate.php` runs the same checks headlessly and exits non-zero on error.
updated: 2026-07-22
tags: [validation, ci, quality-gate]
files: [guide/lib/Content.php, tools/validate.php, guide/templates/reference.php]
reads: [.vibekb, docs/assets/data/search.json]
writes: []
depends_on: [load-living-model, resolve-relationships]
related_memory: [warning:verification-must-reflect-evidence, decision:honest-provenance-no-auto-update]
---

## In one sentence

Definite structural problems (unknown status, unresolved dependency, missing
diagram asset, contradictory totals) become errors; likely-but-not-certain
problems (an unresolved memory back-link, a diagram without topology) become
warnings — and errors stop a snapshot from shipping.

## Current behavior

`Content::validate()` checks functionality required fields and vocabularies,
`depends_on` targets, memory and file back-links, diagram fields, SVG
well-formedness and accessibility, and — when a diagram has a topology — the full
explainability contract. `tools/validate.php` loads the model through the same
loader and adds publish-time checks: provenance completeness, that
area/record/status totals reconcile, and that a generated `/docs` search index
only points at pages that exist. It prints a report and exits non-zero on any
error.

## Step-by-step flow

1. Load the model (issues accumulate during load and validation).
2. Split issues into errors and warnings.
3. Add provenance, totals, and snapshot-search checks.
4. Print counts and each issue; exit non-zero if any error exists.

## Implementation map

- `guide/lib/Content.php` — `validate()` and `validateTopology()`.
- `tools/validate.php` — the CI-facing wrapper and publish-time checks.
- `guide/templates/reference.php` — renders the same issues in the guide's
  Reference view.

## Data used

- **Reads:** the whole model, plus `docs/assets/data/search.json` when present.
- **Writes:** none.

## Failure cases

- Errors → non-zero exit (blocks generation and CI).
- Malformed JSON / unreadable files → reported, not fatal.

## Safe to change

Adding a new *warning* is safe. Adding a new *error* changes what fails CI — do
it deliberately and update SCHEMA.md.

## Use caution

Do not downgrade a genuine structural error to a warning to make CI pass. The
whole point is that the model cannot ship internally contradictory.

## Why it works this way

The guide's Reference view and CI run the identical checks, so "it validates
locally" and "it passes CI" can never diverge.
