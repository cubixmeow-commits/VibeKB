---
id: start-project
type: functionality
title: Start a project
area: projects-progress
summary: Creates a project from an executable Cookbook and sends the user straight to the Pantry — with a server-side gate that preview Cookbooks can never start.
status: implemented
verification: verified-from-source
user_facing: true
trigger: A verified user posts a Cookbook slug to "/projects".
updated: 2026-07-16
tags: [projects, write]
files: [app/Controllers/ProjectController.php, app/Models/Project.php, app/Models/Cookbook.php]
reads: [cookbooks]
writes: [projects]
config: []
depends_on: [verify-email, view-cookbook-detail]
related_memory: [constraint:no-checkout-v1]
---

## In one sentence

Pick an executable Cookbook and a new project is created, dropping you at its
Pantry.

## Current behavior

`ProjectController::create()` requires a verified account, resolves the posted
`cookbook` slug, and **re-checks `is_executable` server side** — a preview
Cookbook is refused even if a client forged the request. It creates the project
(`Project::create`) and redirects to the Pantry.

## Step-by-step flow

1. Verified user posts a Cookbook slug to `/projects` (from the detail page).
2. `Cookbook::findBySlug()` resolves it (missing → back to marketplace).
3. `is_executable !== 1` → refused with a notice (server-side gate).
4. `Project::create()` writes the project.
5. Redirect to `/projects/{id}/pantry`.

## Implementation map

- `app/Controllers/ProjectController.php` — `create()`.
- `app/Models/Project.php` — `create`.

## Data used

- **Inputs:** the Cookbook slug.
- **Writes:** one `projects` row (owner = current user).

## Dependencies

A verified account (`verify-email`); reached from **View a Cookbook detail**.

## Dependents

**Fill the Pantry**, then the whole Runner loop.

## Failure cases

- Unknown Cookbook → redirect to marketplace with an error.
- Preview ("coming soon") Cookbook → refused (no checkout exists — see the
  `no-checkout-v1` constraint).

## Current state

- **Status:** implemented. **Verification:** verified from source
  (`ProjectController::create` traced).

## Use caution

The executable gate is the boundary that keeps preview Cookbooks non-runnable;
do not move it to the view layer.

## Related functionality

- View a Cookbook detail
- Fill the Pantry
