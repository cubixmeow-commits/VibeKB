---
id: view-admin-overview
type: functionality
title: View the admin overview
area: administration
summary: A read-only operations dashboard — totals, recent users and projects, and simulation status — gated by a server-side admin role.
status: implemented
verification: verified-from-source
user_facing: true
trigger: An admin opens "/admin".
updated: 2026-07-16
tags: [admin, dashboard, read]
files: [app/Controllers/AdminController.php, app/Views/admin/index.php, app/Services/SiteStats.php, app/Core/Auth.php]
reads: [users, projects, artifacts, exports, cookbooks, recipes, simulation_runs]
writes: []
config: []
depends_on: [sign-in]
related_memory: [constraint:cli-only-admin]
---

## In one sentence

A read-only snapshot of the whole site — counts, recent activity, and demo
simulation status — that only an admin can open.

## Current behavior

`AdminController::index()` calls `Auth::requireAdmin()` (server-side role check
on every request), then loads totals (users, projects, completed, approved,
exports), the 12 most recently updated projects with approved/recipe counts, the
`SiteStats::adminBundle()` simulation figures, and recent users. It only reads.

## Step-by-step flow

1. Admin opens `/admin`.
2. `Auth::requireAdmin()` enforces the role (non-admins are refused).
3. Totals and recent activity are queried (read-only).
4. `admin/index` renders the dashboard.

## Implementation map

- `app/Controllers/AdminController.php` — `index()`.
- `app/Services/SiteStats.php` — `adminBundle()` and metrics.
- `app/Core/Auth.php` — `requireAdmin`.

## Data used

- **Reads:** aggregate counts across most tables. **Writes:** none.

## Dependencies

An admin session. Admin accounts exist only via `scripts/seed.php`.

## Failure cases

- Non-admin → refused by `requireAdmin`.

## Current state

- **Status:** implemented. **Verification:** verified from source
  (`AdminController` traced; the aggregate SQL is inline in the controller and
  `SiteStats`).

## Why it works this way

Admin is CLI-provisioned and read-only on the web, so there is no privileged
web surface to attack — see the `cli-only-admin` constraint.

## Related functionality

- Demo Mode & simulation
