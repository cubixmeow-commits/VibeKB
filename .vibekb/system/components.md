---
id: components
type: system
title: Components
summary: The content model, the loader, the shared template set, the two output modes, the validator and topology test, and the self-maintenance CLI.
updated: 2026-07-22
---

## Content model — `.vibekb/`

Repository-owned Markdown-plus-front-matter records and small JSON manifests. The
source of truth. No database.

## Loader — `guide/lib/Content.php`

Reads, parses (`FrontMatter.php`, `Markdown.php`), relationship-resolves, and
validates the model. All filesystem access confined to the content root. Used by
the guide and every tool, so nothing can disagree about what the model says.

## Renderer — `guide/templates/` + `guide/lib/`

One template set rendered through `layout.php`. Shared navigation/routes/titles
(`nav.php`), URL strategy (`UrlStrategy.php`), provenance (`Provenance.php`),
search index (`search.php`), and vocabularies/helpers (`helpers.php`).

## Output modes

- **Mode A — dynamic guide** (`guide/index.php`): live PHP, reads `.vibekb/` per
  request.
- **Mode B — static snapshot** (`tools/generate-static.php` → `/docs`): the same
  templates rendered to disk with relative links.

## Validation — `tools/validate.php` + `tools/test-topology.php`

Headless model validation (CI gate) and a fixture test proving the
explainable-diagram contract is enforced.

## Self-maintenance CLI — `tools/vibekb.php`

The agent's entry point to the lifecycle: `status`, `check` (drift + validate +
snapshot sync), `affected`, `validate`, `generate`.

## Examples — `examples/`

Bundled models of other applications (SousMeow) and field-test material (the
StopPR audit). Demonstration and fixtures — never the active model.
