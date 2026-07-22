---
id: request-flow
type: system
title: Request flow
summary: A browser request enters guide/index.php, which loads the model once, resolves the view, and renders the shared layout — no rewrite rules, no build step.
updated: 2026-07-22
---

## The dynamic path (Mode A)

1. The browser requests `guide/index.php?view=…` (query-string routing; no
   rewrite rules).
2. The front controller registers the dynamic URL strategy and loads `Content`
   from `.vibekb/` (or a `VIBEKB_CONTENT_ROOT` example).
3. `?view=` is resolved against the shared route whitelist; `functionality` is
   special-cased for index vs detail by `?id`; a live search JSON endpoint is
   handled inline.
4. The chosen template is rendered inside `layout.php` with shared navigation.
5. Output is escaped; provenance is stamped; the page returns.

## The static path (Mode B)

`tools/generate-static.php` runs the same templates offline, swapping in the
static URL strategy, and writes the pages to `/docs`. There is no live request —
the snapshot is the dynamic guide, frozen at a commit.

## Error posture

Unknown view or record → `not-found` (404). Model load failure → 500 with a
dev/prod-appropriate message. Dev mode (`VIBEKB_DEV` or localhost) shows full
errors and a validation banner.
