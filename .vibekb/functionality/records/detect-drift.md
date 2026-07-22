---
id: detect-drift
type: functionality
title: Detect drift between code and model
area: agent-workflow
summary: `php tools/vibekb.php check` reports where the model and the repository have diverged — model references to files that no longer exist, source files changed since the recorded commit, functionality those changes likely affect, and whether the /docs snapshot is stale — separating what was detected mechanically from what still needs an agent to interpret.
status: implemented
verification: verified-by-test
user_facing: true
trigger: A coding agent runs `php tools/vibekb.php check` before committing, or CI runs it.
updated: 2026-07-22
tags: [cli, drift, ci, self-maintenance]
files: [tools/vibekb.php, tools/validate.php, tools/generate-static.php]
reads: [.vibekb, guide, tools, docs]
writes: []
depends_on: [validate-model, find-affected-functionality, generate-static-snapshot]
related_memory: [warning:model-can-drift-from-code, warning:docs-is-generated-never-hand-edit, decision:honest-provenance-no-auto-update]
---

## In one sentence

`check` is the consistency gate: it runs full validation, verifies every file the
model points at still exists, lists source changes since the model's recorded
commit and the functionality they likely touch, and confirms `/docs` still
matches a fresh render.

## Current behavior

`check` produces four sections:

1. **Model validation** — the same errors/warnings as `validate` (definite
   structural problems are errors).
2. **Broken file references (detected)** — any path in a functionality `files[]`,
   `important-files.json`, or a diagram topology that does not exist on disk. This
   is a definite error: the model claims code that is gone.
3. **Source changes since the recorded commit (detected → needs interpretation)**
   — files changed under the modelled source areas (`guide/`, `tools/`,
   `index.php`, deployment config) since `manifest.provenance.source_commit`, plus
   any uncommitted working-tree changes, each mapped to the functionality it
   likely affects (see **Find affected functionality**). Unmapped changed files
   are listed as "may need a new or updated record." This section is
   informational — VibeKB detected a change but cannot interpret its meaning.
4. **Snapshot sync (detected)** — regenerates the static site into a temporary
   directory and compares it (ignoring the volatile generation timestamp) against
   `/docs`; a difference means `/docs` is stale and should be regenerated.

Exit code is non-zero only on **definite errors** (validation errors or broken
file references). Drift and snapshot staleness are reported as warnings so a
harmless code change is never blocked; `--strict` promotes snapshot staleness to
a failure for CI that publishes `/docs`.

## Step-by-step flow

1. Load and validate the model.
2. Check every referenced file path exists.
3. `git diff --name-only <source_commit>..HEAD` + `git status --porcelain`, filter
   to modelled source areas, map each to functionality.
4. Regenerate into a temp dir and diff against `/docs`.
5. Print sections; exit non-zero on definite errors (or on stale `/docs` under
   `--strict`).

## Implementation map

- `tools/vibekb.php` — the `check` subcommand and the drift/sync logic.
- `tools/generate-static.php` — invoked (into a temp dir via `VIBEKB_DOCS_OUT`)
  for the sync comparison.

## Failure cases

- Not a git repository, or the recorded commit is unknown → the change section
  degrades to "cannot compare (no git history for the recorded commit)" instead
  of failing.

## Use caution

`check` reports **likely** affected functionality from existing `files[]`
back-links. It is a heuristic: a change to a file no record lists is surfaced as
unmapped, never silently treated as harmless. Do not treat a clean drift section
as proof the model is correct — only that nothing mechanical is inconsistent.

## Why it works this way

Honest automation means being explicit about the boundary between *detected*
(mechanical: git diff, path existence, render diff) and *interpreted* (an agent
deciding what a change means for the model). `check` never crosses that line on
its own.
