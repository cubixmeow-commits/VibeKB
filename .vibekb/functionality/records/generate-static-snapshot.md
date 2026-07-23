---
id: generate-static-snapshot
type: functionality
title: Generate the static snapshot
area: static-publishing
summary: `php tools/generate-static.php` (or the installed copy under `.vibekb/runtime/tools/`) renders the same templates as the live guide into a self-contained static site with relative, subpath-safe links — refusing to build if the model has validation errors, and clearly stamped as generated output that does not update itself. Self-hosted VibeKB publishes to `/docs`; a consolidated target install publishes to `.vibekb/generated/` so it never collides with the project's own `docs/`.
status: implemented
verification: verified-from-source
user_facing: true
trigger: A developer runs `php tools/generate-static.php` (self-hosted) or `php .vibekb/runtime/tools/generate-static.php` / `vibekb generate` (target install) to refresh the snapshot.
updated: 2026-07-23
tags: [static, docs, mode-b, github-pages, repository-safety]
files: [tools/generate-static.php, guide/lib/UrlStrategy.php, guide/lib/nav.php, guide/lib/workspace.php]
reads: [.vibekb, guide/templates, guide/assets]
writes: [docs, .vibekb/generated]
config: [VIBEKB_GENERATED, VIBEKB_DOCS_OUT]
depends_on: [render-guide, validate-model]
related_memory: [decision:two-modes-one-source, warning:docs-is-generated-never-hand-edit, decision:repository-safety-consolidation]
---

## In one sentence

The generator swaps only the URL strategy (to emit relative, subpath-safe links)
and writes the rendered HTML to disk — there is no second template system, so the
snapshot is the live guide, frozen.

## Current behavior

It resolves the content root and project root via `guide/lib/workspace.php` so it
works from either the self-hosted layout (`<repo>/tools`) or a consolidated
install (`<repo>/.vibekb/runtime/tools`). It loads the model, refuses to build if
there are any validation errors, records the generation event (mode `static`,
generated time, generator commit/branch), then renders every view — overview,
each functionality record, the section pages, each memory record — into the
output directory, copies CSS/JS from the **runtime** guide assets (never from a
missing `$repoRoot/guide` path), copies diagram SVGs, writes the search index
built for the static location, and drops a `.nojekyll`. It cleans only the
generated site, leaving other files in the output directory alone.

Default output: `<repo>/docs` when the active model is self-hosted; otherwise
`.vibekb/generated` so a target repository's own `docs/` is never touched.
`VIBEKB_DOCS_OUT` overrides the destination (used by the drift check to render
into a temp dir); `VIBEKB_GENERATED` pins the timestamp.

## Step-by-step flow

1. Locate content root / project root (layout-aware).
2. Load the model; abort non-zero if it has errors.
3. Stamp the generation event.
4. Render the full page inventory through `layout` with the static URL strategy.
5. Copy assets from `$runtimeRoot/guide/assets` and diagram SVGs; write
   `search.json`; write `.nojekyll`.

## Implementation map

- `tools/generate-static.php` — orchestration, layout-aware defaults, page inventory.
- `guide/lib/workspace.php` — content/project root resolution.
- `guide/lib/UrlStrategy.php` — `StaticUrlStrategy` (relative links).
- `guide/lib/nav.php` — the shared inventory both modes render.

## Failure cases

- Any model validation error → refuses to build (a snapshot must not ship a
  broken model).
- Missing runtime guide assets → copy fails (should not happen after a valid
  install); pages may still emit without CSS/JS.

## Use caution

The snapshot directory is **generated output**. Never hand-edit it — change
`.vibekb/` and regenerate. In this self-hosted repo that directory is `/docs`;
in a target install it is `.vibekb/generated/`. The drift check flags a
hand-edited or stale snapshot.

## Why it works this way

Publishing the exact live guide as a static file set means the public site is a
true demonstration of the model, not a separately-authored marketing page.
Consolidated installs keep that snapshot inside `.vibekb/` so they never collide
with a project's own documentation tree.
