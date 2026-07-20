---
id: create-idea
type: functionality
title: Create an idea
area: primary-workflow
summary: Lets the operator capture a new SaaS idea as a stored record with a title, notes, and starting priority.
status: implemented
verification: verified-from-source
user_facing: true
trigger: The operator submits the "New idea" form.
created: 2026-01-20
updated: 2026-07-15
tags: [ideas, forms, write]
files: [public/create-idea.php, src/IdeaService.php, src/IdeaRepository.php, src/Database.php]
reads: []
writes: [ideas]
config: [IDEAS_DB_PATH]
depends_on: [initialize-database]
related_memory: [decision:sqlite-over-mysql, assumption:plain-text-ideas, warning:read-write-path-drift]
---

## In one sentence

The operator types a title and notes, submits the form, and the idea is saved
to the database and becomes visible in the list.

## User experience

The operator opens **New idea**, sees a short form (title, notes, and a
priority selector), fills it in, and presses **Save**. On success they land
back on the ideas list with the new idea already showing at its priority
position.

## Current behavior

A `POST` to `create-idea.php` is validated, wrapped into an `Idea` by
`IdeaService`, written to the `ideas` table by `IdeaRepository`, and the
operator is redirected to the list. The new record starts with status `inbox`.

## Step-by-step flow

1. Operator submits the New idea form.
2. The request reaches `public/create-idea.php`.
3. Required fields (title) are validated; empty titles are rejected and the
   form is re-rendered with the entered values.
4. `IdeaService::create()` applies the rules: trims input, defaults status to
   `inbox`, and normalises priority to an integer.
5. `IdeaRepository::insert()` writes the record to the SQLite `ideas` table.
6. The operator is redirected (`303`) to the ideas list.

## Implementation map

- `public/create-idea.php` — renders the form (GET) and handles the submit
  (POST).
- `src/IdeaService.php` — validation and application rules.
- `src/IdeaRepository.php` — the `INSERT` statement.
- `src/Database.php` — supplies the PDO connection.

## Data used

- **Inputs:** `title` (required), `notes` (optional), `priority` (optional
  integer, default 100).
- **Writes:** one row in the `ideas` table (`title`, `notes`, `status`,
  `priority`, `created_at`, `updated_at`).
- **Output:** redirect to the list; no direct render of the new record.

## Dependencies

Requires the database to exist and the `ideas` table to be present — see
**Initialize the database**.

## Dependents

**Browse ideas** and **View an idea** display what this function writes.

## Failure cases

- Empty title → form re-rendered with a validation message; nothing saved.
- Database file not writable → PDO exception; the operator sees a generic
  error page (details are logged, not shown).
- Very long notes → stored as-is; no length cap is enforced yet.

## Configuration

`IDEAS_DB_PATH` selects which SQLite file receives the write. If unset, the
app falls back to `data/ideas.sqlite` beside the application.

## Current state

- **Status:** implemented.
- **Verification:** verified from source (the insert path was read end to end).
- **Limitations:** no CSRF token (acceptable only because the app is
  single-user and unauthenticated — see the `half-auth-not-multiuser`
  warning); no note length limit.

## Safe to change

Copy and form field labels are safe to edit. Adding an optional field is safe
**only if** you update validation, the `INSERT`, and every read path in the
same change (see the `read-write-path-drift` warning).

## Use caution

Changing the `ideas` table shape requires a migration and touches every read
path. Do not add a required column without a default, or existing rows and the
create form will diverge.

## Why it works this way

Ideas are stored as plain text with no upload handling — see the
`plain-text-ideas` assumption. SQLite was chosen for single-operator
simplicity — see the `sqlite-over-mysql` decision.

## Change history

- 2026-07-15 — create form now sets a default `status` of `inbox` as part of
  the status-lifecycle work (`added-status-field`).

## Current AI work

Not currently under active change. See **Current AI work** for the live focus.

## Related functionality

- Browse ideas
- View an idea
- Change an idea's status
