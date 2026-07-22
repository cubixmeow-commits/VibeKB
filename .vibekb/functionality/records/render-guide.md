---
id: render-guide
type: functionality
title: Render the dynamic guide
area: dynamic-guide
summary: A single PHP front controller routes by `?view=`, loads the model, and renders one shared template set into the browser — the live guide (Mode A) that reads `.vibekb/` on every request with no build step and no rewrite rules.
status: implemented
verification: verified-from-source
user_facing: true
trigger: A browser requests guide/index.php (optionally with ?view= and ?id=).
updated: 2026-07-22
tags: [guide, routing, rendering, mode-a]
files: [guide/index.php, guide/templates/layout.php, guide/lib/nav.php, guide/lib/UrlStrategy.php, guide/lib/helpers.php]
reads: [.vibekb]
writes: []
config: [VIBEKB_DEV, VIBEKB_CONTENT_ROOT]
depends_on: [load-living-model]
related_memory: [decision:two-modes-one-source, constraint:no-build-step-portable]
---

## In one sentence

`guide/index.php` is a whitelist router: it maps `?view=` to a template, loads
the model once, and renders the shared `layout` — the same templates the static
generator uses, so the two modes cannot drift.

## Current behavior

The front controller sets the dynamic URL strategy, loads `Content` (from the
repository's `.vibekb/`, or a `VIBEKB_CONTENT_ROOT` example), and routes: an
unknown view or missing record renders `not-found` with the right HTTP status;
`functionality` is special-cased for index vs detail by `?id`; a dev flag
(`VIBEKB_DEV`, or localhost) enables full errors and the validation banner.
Navigation, routes, and page titles come from `guide/lib/nav.php` so both modes
present the identical inventory.

## Step-by-step flow

1. Register the dynamic URL strategy and generation context.
2. Load the model (500 with a safe message if it throws).
3. Resolve `?view=`; special-case `functionality` and the live search JSON.
4. Pick the template and title; render `layout` with the shared nav.

## Implementation map

- `guide/index.php` — routing and error posture.
- `guide/lib/nav.php` — routes, nav, titles (shared with the generator).
- `guide/templates/layout.php` — the page shell.
- `guide/lib/UrlStrategy.php` — dynamic vs static URL building.

## Failure cases

- Unknown view / unknown record id → `not-found` (404).
- Model load failure → 500 with a dev/prod-appropriate message.

## Safe to change

Template markup and copy are safe. Adding a view means adding it in `nav.php`
once, so both modes pick it up.

## Use caution

Escape all output (`h()`); never trust `?id`/`?view` beyond the existing
sanitisation.

## Why it works this way

Query-string routing needs no rewrite rules, so the guide runs in a subfolder on
shared hosting; one template set shared with the generator is what makes "two
modes, one source" true rather than aspirational.
