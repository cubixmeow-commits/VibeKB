---
id: view-marketing-home
type: functionality
title: View the marketing home
area: explore-discovery
summary: The public landing page that presents the featured Cookbook as a full artifact plus the catalog and a curated money shelf.
status: implemented
verification: verified-from-source
user_facing: true
trigger: A visitor opens "/".
updated: 2026-07-16
tags: [marketing, discovery, public]
files: [app/Controllers/MarketingController.php, app/Views/marketing/home.php, app/Models/Cookbook.php, app/Services/CollectionResolver.php]
reads: [cookbooks, recipes, cookbook_stages, sousmeow_collections]
writes: []
config: [app.base_path]
depends_on: []
related_memory: [decision:categories-collections-taxonomy]
---

## In one sentence

The front door: the featured Cookbook shown in full, the catalog, and a curated
"money decisions" shelf, all without signing in.

## Current behavior

`MarketingController::home()` loads the marketplace catalog
(`Cookbook::marketplace()`), the featured Cookbook with its stages and recipes,
and — when it clears its `min_display_count` — the `money-major-decisions`
collection resolved via `CollectionResolver::cookbooksFor()`. It renders
`marketing/home`.

## Step-by-step flow

1. Visitor opens `/`.
2. `MarketingController::home()` runs (no auth required).
3. The featured Cookbook, its stages, and its recipes are loaded.
4. The money collection is included only if it has enough qualifying Cookbooks.
5. `marketing/home` renders with the catalog and shelves.

## Implementation map

- `app/Controllers/MarketingController.php` — the controller.
- `app/Views/marketing/home.php` — the page.
- `app/Models/Cookbook.php` — `marketplace()`, `featured()`.
- `app/Services/CollectionResolver.php` — resolves the shelf membership.

## Data used

- **Reads:** `cookbooks`, `recipes`, `cookbook_stages`, and collection tables.
- **Writes:** none.

## Dependencies

Relies on seeded catalog content (see **Seed & sync content**).

## Dependents

Links into **View a Cookbook detail** and, for signed-in users, the Runner.

## Failure cases

- Empty catalog → the page renders without shelves (no error).

## Current state

- **Status:** implemented. **Verification:** verified from source
  (`MarketingController`, `SiteStats` for the activity dashboard).

## Safe to change

Copy and the shelf composition are safe to adjust.

## Use caution

The money shelf's visibility is gated by `min_display_count`; removing that gate
would surface a half-populated shelf.

## Related functionality

- View a Cookbook detail
- Browse the marketplace
