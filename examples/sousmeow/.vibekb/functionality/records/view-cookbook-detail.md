---
id: view-cookbook-detail
type: functionality
title: View a Cookbook detail
area: explore-discovery
summary: The full Cookbook page — stages, every Recipe with its Quality Checks and Pantry fields, related Cookbooks, and the start action.
status: implemented
verification: verified-from-source
user_facing: true
trigger: A visitor opens "/cookbooks/{slug}".
updated: 2026-07-16
tags: [discovery, cookbook, public]
files: [app/Controllers/MarketplaceController.php, app/Views/marketplace/show.php, app/Models/Cookbook.php, app/Models/Recipe.php, app/Models/CookbookStage.php, app/Models/PantryField.php]
reads: [cookbooks, cookbook_stages, recipes, recipe_checks, pantry_fields, sousmeow_categories, sousmeow_collections]
writes: []
config: []
depends_on: []
related_memory: [decision:categories-collections-taxonomy]
---

## In one sentence

Everything about one Cookbook — its stages, Recipes, Quality Checks, Pantry
fields, category, collections, and related Cookbooks — with the button to start.

## Current behavior

`MarketplaceController::show($slug)` loads the Cookbook by slug (404 if absent),
its recipes and their checks, the surfaced Collections it belongs to, its
Pantry fields, and a set of related Cookbooks. Related selection is
special-cased for the money and career collections (via helper files) and
otherwise falls back to same-category peers. The start action posts to
`/projects`.

## Step-by-step flow

1. Visitor opens `/cookbooks/{slug}`.
2. `Cookbook::findBySlug()` loads it, or a 404 view renders.
3. Recipes, per-recipe checks, stages, and Pantry fields load.
4. Surfaced collections and related Cookbooks are computed.
5. `marketplace/show` renders; the start button posts to **Start a project**.

## Implementation map

- `app/Controllers/MarketplaceController.php` — `show()`.
- `app/Models/Cookbook.php`, `Recipe.php`, `CookbookStage.php`,
  `PantryField.php`, `Category.php`, `Collection.php`.
- `database/seeds/career_helpers.php`, `money_helpers.php` — related-slug logic.

## Data used

- **Reads:** the Cookbook and its recipes/checks/fields/stages plus taxonomy.
- **Writes:** none.

## Dependencies

Seeded catalog; the discovery taxonomy (`categories-collections-taxonomy`).

## Dependents

**Start a project** begins from this page.

## Failure cases

- Unknown slug → 404 view.

## Current state

- **Status:** implemented. **Verification:** verified from source
  (`MarketplaceController::show` traced end to end).

## Safe to change

Layout, related-Cookbook count, and copy are safe to adjust.

## Use caution

The start button only works for executable Cookbooks; the server re-checks this
in **Start a project** (a preview Cookbook can never start).

## Related functionality

- Start a project
- Browse by category
- View a collection
