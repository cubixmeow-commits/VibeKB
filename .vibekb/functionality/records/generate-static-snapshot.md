---
id: generate-static-snapshot
type: functionality
title: Generate the static snapshot
area: static-publishing
summary: `php tools/generate-static.php` renders the same templates as the live guide into a self-contained `/docs` site with relative, subpath-safe links — refusing to build if the model has validation errors, and clearly stamped as generated output that does not update itself.
status: implemented
verification: verified-from-source
user_facing: true
trigger: A developer runs `php tools/generate-static.php` (locally or in CI) to refresh /docs.
updated: 2026-07-22
tags: [static, docs, mode-b, github-pages]
files: [tools/generate-static.php, guide/lib/UrlStrategy.php, guide/lib/nav.php]
reads: [.vibekb, guide/templates, guide/assets]
writes: [docs]
config: [VIBEKB_GENERATED, VIBEKB_DOCS_OUT]
depends_on: [render-guide, validate-model]
related_memory: [decision:two-modes-one-source, warning:docs-is-generated-never-hand-edit]
---

## In one sentence

The generator swaps only the URL strategy (to emit relative, subpath-safe links)
and writes the rendered HTML to disk — there is no second template system, so the
snapshot is the live guide, frozen.

## Current behavior

It loads the model, refuses to build if there are any validation errors, records
the generation event (mode `static`, generated time, generator commit/branch),
then renders every view — overview, each functionality record, the section pages,
each memory record — into the output directory, copies CSS/JS and diagram SVGs,
writes the search index built for the static location, and drops a `.nojekyll`.
It cleans only the generated site, leaving other files in `/docs` alone. The
output directory defaults to `/docs` but honours `VIBEKB_DOCS_OUT` (used by the
drift check to render into a temp dir); `VIBEKB_GENERATED` pins the timestamp.

## Step-by-step flow

1. Load the model; abort non-zero if it has errors.
2. Stamp the generation event.
3. Render the full page inventory through `layout` with the static URL strategy.
4. Copy assets and diagram SVGs; write `search.json`; write `.nojekyll`.

## Implementation map

- `tools/generate-static.php` — orchestration and page inventory.
- `guide/lib/UrlStrategy.php` — `StaticUrlStrategy` (relative links).
- `guide/lib/nav.php` — the shared inventory both modes render.

## Failure cases

- Any model validation error → refuses to build (a snapshot must not ship a
  broken model).

## Use caution

`/docs` is **generated output**. Never hand-edit it — change `.vibekb/` and
regenerate. The drift check flags a hand-edited or stale `/docs`.

## Why it works this way

Publishing the exact live guide as a static file set means the public site is a
true demonstration of the model, not a separately-authored marketing page.
