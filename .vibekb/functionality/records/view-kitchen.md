---
id: view-kitchen
type: functionality
title: View My Kitchen
area: projects-progress
summary: The signed-in home — your projects with a prominent "continue" card for the most recent unfinished one, plus featured Cookbooks.
status: implemented
verification: verified-from-source
user_facing: true
trigger: A signed-in user opens "/kitchen".
updated: 2026-07-16
tags: [dashboard, projects, signed-in]
files: [app/Controllers/KitchenController.php, app/Views/kitchen/index.php, app/Models/Project.php, app/Models/Cookbook.php]
reads: [projects, cookbooks]
writes: []
config: []
depends_on: [sign-in]
related_memory: []
---

## In one sentence

Your projects in one place, with the most recent unfinished project surfaced as
a big "continue" card.

## Current behavior

`KitchenController::index()` requires login, lists the user's projects
(`Project::listForUser`), picks the first not-yet-completed project as the
`continue` card, and includes featured Cookbooks for starting something new.

## Step-by-step flow

1. Signed-in user opens `/kitchen`.
2. `Project::listForUser()` returns their projects (recently touched first).
3. The first project with `completed_at === null` becomes the continue card.
4. `kitchen/index` renders projects + featured Cookbooks.

## Implementation map

- `app/Controllers/KitchenController.php` — `index()`.
- `app/Models/Project.php` — `listForUser`.
- `app/Models/Cookbook.php` — `featured`.

## Data used

- **Reads:** `projects`, `cookbooks`. **Writes:** none.

## Dependencies

Requires a signed-in session.

## Dependents

Entry point to **Start a project**, the Runner, and export.

## Failure cases

- No projects → an empty state inviting the first project (not an error).

## Current state

- **Status:** implemented. **Verification:** verified from source
  (`KitchenController` traced).

## Related functionality

- Start a project
- Track project progress
