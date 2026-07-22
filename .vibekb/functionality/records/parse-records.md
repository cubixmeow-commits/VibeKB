---
id: parse-records
type: functionality
title: Parse records (front matter + Markdown)
area: living-model
summary: A small front-matter parser and a pragmatic Markdown subset turn each `.vibekb/` file into structured metadata plus rendered HTML, without pulling in a Markdown library or a build step.
status: implemented
verification: verified-from-source
user_facing: false
trigger: The loader reads any `.md` record.
updated: 2026-07-22
tags: [parsing, markdown, front-matter]
files: [guide/lib/FrontMatter.php, guide/lib/Markdown.php]
reads: []
writes: []
depends_on: []
related_memory: [constraint:no-build-step-portable]
---

## In one sentence

Each record file is split into a `---` fenced front-matter block (scalars,
quoted strings, booleans, ints, inline and block lists) and a Markdown body that
is rendered by a deliberately small, dependency-free renderer.

## Current behavior

`FrontMatter::parse()` reads the leading fenced block and returns
`['meta' => ..., 'body' => ...]`. `Markdown::toHtml()` supports the subset the
guide actually needs — headings, lists, tables, fenced code, emphasis, links,
and blockquotes — and escapes output. Anything outside the subset is rendered as
plain text rather than executed.

## Implementation map

- `guide/lib/FrontMatter.php` — the value-form parser (no YAML dependency).
- `guide/lib/Markdown.php` — the Markdown subset renderer.

## Failure cases

- A record with no front-matter block still parses (empty meta, full body).
- Unsupported Markdown constructs degrade to text; they are never a hard error.

## Use caution

This is **not** full CommonMark. If a record needs a construct the renderer does
not support, extend the renderer deliberately rather than assuming it works — and
keep escaping first.

## Why it works this way

Avoiding a Markdown library and a build step is what keeps VibeKB deployable on
plain PHP 8.2 shared hosting. The subset is a constraint, documented as one.
