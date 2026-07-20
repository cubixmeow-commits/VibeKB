---
id: browse-ideas
type: functionality
title: Browse ideas
area: primary-workflow
summary: Shows every idea in one list, ordered by priority, with an empty state when there are none.
status: partial
verification: verified-from-source
user_facing: true
trigger: The operator opens the app home page.
created: 2026-01-20
updated: 2026-07-16
tags: [ideas, list, read]
files: [public/index.php, src/IdeaRepository.php, templates/ideas-list.php]
reads: [ideas]
writes: []
config: [IDEAS_DB_PATH]
depends_on: [initialize-database]
related_memory: [discovery:blank-list-ordering, warning:read-write-path-drift]
---

## In one sentence

Opening the app shows all ideas in a single list, most important first.

## User experience

The home page is the list. Each row shows the idea's title, status, and a
snippet of notes. If there are no ideas yet, a friendly empty state invites the
operator to create the first one.

## Current behavior

`public/index.php` asks `IdeaRepository::all()` for every idea ordered by
`priority ASC, created_at DESC`, then renders `templates/ideas-list.php`. There
is no pagination and no manual reordering UI yet.

## Step-by-step flow

1. Operator opens the app home page.
2. `public/index.php` runs.
3. `IdeaRepository::all()` selects every row ordered by priority then recency.
4. If the result is empty, the empty-state partial renders.
5. Otherwise `templates/ideas-list.php` renders one row per idea.

## Implementation map

- `public/index.php` — the list controller and app entry point.
- `src/IdeaRepository.php` — `all()` query and ordering.
- `templates/ideas-list.php` — the row and empty-state markup.

## Data used

- **Reads:** all rows from `ideas` (`id`, `title`, `notes`, `status`,
  `priority`, `created_at`).
- **Writes:** none.

## Dependencies

Requires the database and `ideas` table — see **Initialize the database**.

## Dependents

Nothing depends on browse directly, but it is the entry point that links to
**View an idea** and **Create an idea**.

## Failure cases

- Empty database → intentional empty state (not an error). This was the source
  of a real bug — see the `blank-list-ordering` discovery.
- Missing `ideas` table → PDO exception, generic error page.

## Configuration

`IDEAS_DB_PATH` selects which database is read.

## Current state

- **Status:** partial — the list and ordering work, but there is no UI to
  change priority after creation, so "browse by priority" is only half the
  intended feature.
- **Verification:** verified from source.
- **Limitations:** no pagination; large lists render in full.

## Safe to change

Row markup and the empty-state copy are safe to edit. The `ORDER BY` clause is
safe to tune.

## Use caution

If you add a column to the list, confirm it exists on every row — mixing an
old database with new read code reproduces the `blank-list-ordering` class of
bug.

## Why it works this way

The single ordered list is deliberate: the product promise is "see which ideas
matter most at a glance" (see the project intent). Pagination was skipped
because a single operator rarely exceeds a few hundred ideas.

## Change history

- 2026-07-16 — list rows now show the idea `status` badge after the
  status-field change (`added-status-field`).

## Current AI work

Not currently under active change.

## Related functionality

- Create an idea
- View an idea
- Export ideas
