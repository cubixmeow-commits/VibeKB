---
id: detect-drift
type: functionality
title: Detect drift between code and model
area: agent-workflow
summary: `php tools/vibekb.php check` reports where the model, the repository, and the front-door docs have diverged — model references to files that no longer exist, source files changed since the recorded commit, functionality those changes likely affect, whether the /docs snapshot is stale, and whether the root narrative docs still name real CLI commands, resolvable links, and an accurate .vibekb/ structure block — separating what was detected mechanically from what still needs an agent to interpret.
status: implemented
verification: verified-manually
user_facing: true
trigger: A coding agent runs `php tools/vibekb.php check` before committing, or CI runs it.
updated: 2026-07-23
tags: [cli, drift, ci, self-maintenance]
files: [tools/vibekb.php, tools/validate.php, tools/generate-static.php]
reads: [.vibekb, guide, tools, docs, internal]
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

`check` produces five sections:

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
5. **Documentation claims (detected)** — the doc-side counterpart to broken file
   references: it lints the repository's root-level narrative docs (`README.md`,
   `CLAUDE.md`, and the rest) so VibeKB watches its own front door. Three
   mechanical checks: (a) every `php …/vibekb.php <sub>` and `vibekb <sub>`
   invocation shown in a fenced code block must name a real subcommand — the valid
   sets are parsed from the actual dispatch (this script + `internal/cli/cli.go`)
   so the lint can never disagree with the code; (b) every relative Markdown link
   must resolve on disk; (c) README's `.vibekb/` structure block must match the
   real top-level of `.vibekb/`. A dead command, a dead link, or a structure block
   naming a directory that is gone is a **definite error**; a real directory the
   block merely omits is a warning. Each check degrades to a printed "skipped"
   note — never a false error — when its source of truth is absent (e.g. the Go
   command set in a downstream install with no `internal/cli/cli.go`).

Exit code is non-zero only on **definite errors** (validation errors, broken file
references, or a documentation-claims error). Drift and snapshot staleness are
reported as warnings so a harmless code change is never blocked; `--strict`
promotes snapshot staleness to a failure for CI that publishes `/docs`.

## Step-by-step flow

1. Load and validate the model.
2. Check every referenced file path exists.
3. `git diff --name-only <source_commit>..HEAD` + `git status --porcelain`, filter
   to modelled source areas, map each to functionality.
4. Regenerate into a temp dir and diff against `/docs`.
5. Lint the root `*.md` docs: extract fenced-block CLI invocations and Markdown
   links, resolve them against the parsed command sets and the filesystem, and
   compare README's `.vibekb/` tree to the real directory.
6. Print sections; exit non-zero on definite errors (or on stale `/docs` under
   `--strict`).

## Implementation map

- `tools/vibekb.php` — the `check` subcommand, the drift/sync logic, and
  `vibekb_check_docs_claims()` plus its helpers (`vibekb_known_php_subcommands`,
  `vibekb_known_go_commands`, `vibekb_fenced_blocks`, `vibekb_markdown_links`,
  `vibekb_parse_vibekb_tree`, `vibekb_vibekb_toplevel`).
- `tools/generate-static.php` — invoked (into a temp dir via `VIBEKB_DOCS_OUT`)
  for the sync comparison.
- `internal/cli/cli.go` — read (not modified) to derive the valid `vibekb`
  command set for the documentation-claims check; absent in a downstream install,
  where that sub-check degrades to a skip.

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

The documentation-claims section extends drift detection to the docs that
introduce VibeKB — the exact place a drift-detection tool is least expected to
watch, and where drift is most embarrassing. It stays on the *detected* side of
the line: it can prove a documented command no longer exists, a link is dead, or
a structure block names a directory that is gone, but it never judges whether the
prose is accurate in meaning — that remains an agent's job. Deriving the valid
command sets from the real dispatch (rather than a hardcoded list) is what keeps
the lint itself from becoming a second thing that can drift.
