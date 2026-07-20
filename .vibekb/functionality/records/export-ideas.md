---
id: export-ideas
type: functionality
title: Export ideas
area: data-management
summary: Produces a downloadable CSV file of every idea for backup or use elsewhere.
status: experimental
verification: not-verified
user_facing: true
trigger: The operator clicks "Export CSV".
created: 2026-06-05
updated: 2026-07-01
tags: [ideas, export, output]
files: [public/export.php, src/IdeaRepository.php]
reads: [ideas]
writes: []
config: [IDEAS_DB_PATH]
depends_on: [initialize-database, browse-ideas]
related_memory: [assumption:low-volume-single-writer]
---

## In one sentence

The operator clicks Export and the browser downloads a CSV of every idea.

## User experience

A small **Export CSV** link on the list triggers a file download named
`ideas-export.csv`. The file opens in any spreadsheet tool with one row per
idea.

## Current behavior

`public/export.php` reads all ideas, sends CSV headers
(`Content-Type: text/csv`, `Content-Disposition: attachment`), and streams rows
built with `fputcsv()`.

## Step-by-step flow

1. Operator clicks Export CSV.
2. The request reaches `public/export.php`.
3. `IdeaRepository::all()` selects every idea.
4. CSV headers are sent and a header row is written.
5. Each idea is streamed as a CSV row via `fputcsv()`.

## Implementation map

- `public/export.php` — the export controller.
- `src/IdeaRepository.php` — the `all()` query it reuses.

## Data used

- **Reads:** all `ideas` rows.
- **Output:** a `text/csv` download; nothing is written to the database.

## Dependencies

Requires the database and reuses **Browse ideas**' query.

## Dependents

None.

## Failure cases

- Notes containing commas or newlines — `fputcsv()` should quote these, but the
  behaviour has **not been verified** against real multi-line notes. This is
  why the record is experimental.
- Large exports stream fine because rows are written incrementally.

## Configuration

`IDEAS_DB_PATH` selects which database is exported.

## Current state

- **Status:** experimental.
- **Verification:** needs verification — no test has confirmed correct escaping
  of notes with commas, quotes, or newlines.
- **Limitations:** exports all columns with no field selection.

## Safe to change

The output filename and column order are safe to edit.

## Use caution

Do not hand-build CSV rows with string concatenation; keep using `fputcsv()`
so escaping stays correct. Verify against notes containing `,` `"` and newlines
before marking this implemented.

## Why it works this way

CSV was chosen over a database dump because the assumed volume is low and a
single operator wants a spreadsheet-friendly backup — see the
`low-volume-single-writer` assumption.

## Change history

- 2026-06-05 — first version of CSV export added.

## Current AI work

Not currently under active change. Verifying CSV escaping is a recommended
next task (see the handoff).

## Related functionality

- Browse ideas
