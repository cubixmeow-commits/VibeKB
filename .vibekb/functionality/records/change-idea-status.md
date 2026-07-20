---
id: change-idea-status
type: functionality
title: Change an idea's status
area: data-management
summary: Moves an idea through its lifecycle (inbox, exploring, building, shipped, parked) and records the change time.
status: implemented
verification: verified-manually
user_facing: true
trigger: The operator picks a new status on an idea's detail page.
created: 2026-07-12
updated: 2026-07-18
tags: [ideas, status, write]
files: [public/update-status.php, src/IdeaService.php, src/IdeaRepository.php, templates/idea-detail.php]
reads: [ideas]
writes: [ideas]
config: [IDEAS_DB_PATH]
depends_on: [initialize-database, view-idea]
related_memory: [change:added-status-field, warning:read-write-path-drift, discovery:blank-list-ordering]
---

## In one sentence

The operator selects a new status for an idea and the app records it and the
time it changed.

## User experience

On an idea's detail page there is a small status control. Selecting a new
status and confirming updates the badge immediately (after a redirect) and the
"updated" timestamp reflects the change.

## Current behavior

A `POST` to `update-status.php` validates that the requested status is one of
the allowed lifecycle values, then `IdeaRepository::updateStatus()` writes the
new `status` and refreshes `updated_at`. The operator is redirected back to the
detail page.

## Step-by-step flow

1. Operator selects a new status and submits.
2. The request reaches `public/update-status.php`.
3. `IdeaService::isValidStatus()` checks the value against the allowed set
   (`inbox`, `exploring`, `building`, `shipped`, `parked`).
4. If invalid, the change is rejected and the detail page re-renders unchanged.
5. `IdeaRepository::updateStatus()` writes `status` and `updated_at`.
6. The operator is redirected to the idea's detail page.

## Implementation map

- `public/update-status.php` — the status-change controller.
- `src/IdeaService.php` — the allowed-status guard.
- `src/IdeaRepository.php` — the `UPDATE` statement.
- `templates/idea-detail.php` — the status control.

## Data used

- **Inputs:** `id`, requested `status`.
- **Writes:** `status` and `updated_at` on one `ideas` row.

## Dependencies

Requires the database and is launched from **View an idea**.

## Dependents

**Browse ideas** and **View an idea** display the status this writes.

## Failure cases

- Invalid status value → rejected, nothing written.
- Unknown id → no row updated; operator returns to a not-found page.

## Configuration

`IDEAS_DB_PATH` selects which database is written.

## Current state

- **Status:** implemented.
- **Verification:** verified manually — the lifecycle transitions were clicked
  through during the 2026-07 session, but there is no automated test yet.
- **Limitations:** no history log of past statuses; only the latest is kept.

## Safe to change

The allowed-status labels and the control's copy are safe to edit **as long
as** the allowed-set in `IdeaService` and any validation stay in sync.

## Use caution

Adding or renaming a status value is a two-place change (the allowed set and
any UI) and can strand existing rows on a value that is no longer allowed.

## Why it works this way

The status set is a fixed lifecycle rather than free text so the list can group
and badge reliably. See the `added-status-field` change for how it was
introduced.

## Change history

- 2026-07-18 — the status lifecycle and this control were added
  (`added-status-field`, session `2026-07-status-field`).

## Current AI work

Not currently under active change.

## Related functionality

- View an idea
- Browse ideas
