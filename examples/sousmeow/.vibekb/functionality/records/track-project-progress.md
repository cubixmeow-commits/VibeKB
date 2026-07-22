---
id: track-project-progress
type: functionality
title: Track project progress
area: projects-progress
summary: Routes a project to wherever it actually is — Pantry, the next unfinished Recipe, or export — and handles deletion.
status: implemented
verification: verified-from-source
user_facing: true
trigger: A user opens "/projects/{id}" or deletes a project.
updated: 2026-07-16
tags: [projects, progress, write]
files: [app/Controllers/ProjectController.php, app/Models/Project.php, app/Models/Artifact.php, app/Models/Recipe.php]
reads: [projects, recipes, artifacts]
writes: [projects]
config: []
depends_on: [start-project]
related_memory: []
---

## In one sentence

Opening a project always lands you on the right screen for its current state;
deleting one removes it (its old export files stay on disk but become
undownloadable).

## Current behavior

`ProjectController::show()` scopes the project to the owner (404 otherwise),
then redirects: to the Pantry if `pantry_saved_at` is null, to export if
`completed_at` is set, otherwise to the first Recipe without an approved
artifact (`nextPosition()`). `delete()` removes the project;
`Project::markCompleteIfDone()` (called from the Runner) sets `completed_at`
when every Recipe is approved.

## Step-by-step flow

1. User opens `/projects/{id}`.
2. Ownership is enforced (404 if not theirs).
3. No pantry → Pantry; completed → export; else → next unfinished Recipe.
4. `delete()` removes the project after ownership check.

## Implementation map

- `app/Controllers/ProjectController.php` — `show()`, `delete()`, `nextPosition()`.
- `app/Models/Project.php` — `findForUser`, `markCompleteIfDone`, `delete`.
- `app/Models/Artifact.php` — `statusByRecipe`.

## Data used

- **Reads:** `projects`, `recipes`, `artifacts` statuses.
- **Writes:** `projects` (deletion; completion timestamp).

## Dependencies

Follows **Start a project**.

## Failure cases

- Another user's id → 404 (ids of other users are never revealed).

## Current state

- **Status:** implemented. **Verification:** verified from source
  (`ProjectController::show/delete` traced).

## Use caution

Deletion keeps export zips on disk (they are just no longer downloadable);
`storage/exports/` housekeeping is manual.

## Related functionality

- View My Kitchen
- Export the Project Kit
