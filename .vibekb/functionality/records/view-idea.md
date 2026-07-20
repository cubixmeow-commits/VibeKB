---
id: view-idea
type: functionality
title: View an idea
area: primary-workflow
summary: Opens a single idea to read its full notes, current status, and timestamps.
status: implemented
verification: verified-from-source
user_facing: true
trigger: The operator clicks an idea in the list.
created: 2026-02-02
updated: 2026-07-16
tags: [ideas, detail, read]
files: [public/idea.php, src/IdeaRepository.php, templates/idea-detail.php]
reads: [ideas]
writes: []
config: [IDEAS_DB_PATH]
depends_on: [initialize-database, browse-ideas]
related_memory: [warning:read-write-path-drift]
---

## In one sentence

Clicking an idea opens a page with its full notes, status, and dates.

## User experience

From the list, the operator clicks an idea title and lands on a detail page
showing the full notes (not just a snippet), the current status, and when it
was created and last updated. From here they can change its status.

## Current behavior

`public/idea.php?id=N` fetches one idea by id via
`IdeaRepository::find()` and renders `templates/idea-detail.php`. An unknown or
missing id renders a "not found" page with a `404` status.

## Step-by-step flow

1. Operator clicks an idea in the list.
2. `public/idea.php` reads the `id` query parameter.
3. `IdeaRepository::find($id)` selects that single row.
4. If no row is found, the not-found page renders with HTTP 404.
5. Otherwise `templates/idea-detail.php` renders the full record.

## Implementation map

- `public/idea.php` — the detail controller.
- `src/IdeaRepository.php` — `find()` query (parameterised by id).
- `templates/idea-detail.php` — the detail markup.

## Data used

- **Reads:** one row from `ideas` selected by `id`.
- **Writes:** none.

## Dependencies

Requires the database and is reached from **Browse ideas**.

## Dependents

**Change an idea's status** is initiated from this page.

## Failure cases

- Unknown id → 404 not-found page.
- Non-numeric id → treated as not found (the query binds an integer).

## Configuration

`IDEAS_DB_PATH` selects which database is read.

## Current state

- **Status:** implemented.
- **Verification:** verified from source.
- **Limitations:** no edit-in-place for title/notes yet; only status can be
  changed from here.

## Safe to change

Detail layout and copy are safe to edit.

## Use caution

The `id` must stay bound as a parameter — never interpolate it into SQL.

## Why it works this way

A separate detail page keeps the list lightweight (notes are shown as snippets
in the list, in full here).

## Change history

- 2026-07-16 — detail page now shows the status badge and a status-change
  control (`added-status-field`).

## Current AI work

Not currently under active change.

## Related functionality

- Browse ideas
- Change an idea's status
