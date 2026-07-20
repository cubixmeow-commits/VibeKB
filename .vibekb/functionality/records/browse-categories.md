---
id: browse-categories
type: functionality
title: Browse by category
area: explore-discovery
summary: The stable topic spine — a category index and one shared category page with difficulty and executable/preview filters.
status: implemented
verification: verified-from-source
user_facing: true
trigger: A visitor opens "/categories" or "/categories/{slug}".
updated: 2026-07-16
tags: [discovery, categories, public]
files: [app/Controllers/CategoryController.php, app/Views/categories/index.php, app/Views/categories/show.php, app/Models/Category.php, app/Models/Cookbook.php, app/Services/CollectionResolver.php]
reads: [sousmeow_categories, cookbooks, sousmeow_collections, sousmeow_cookbook_collections]
writes: []
config: []
depends_on: []
related_memory: [decision:categories-collections-taxonomy, warning:legacy-category-column]
---

## In one sentence

Every Cookbook has exactly one category; this is the browsable topic spine, with
filters for difficulty and whether a Cookbook is runnable.

## Current behavior

`CategoryController::index()` renders visible categories with counts plus the
surfaced collections (pulling `start-here` into its own section).
`CategoryController::show($slug)` 404s a hidden/unknown category, then lists the
category's Cookbooks with optional `?level=` (difficulty) and `?show=`
(executable/preview) filters computed from the unfiltered set.

## Step-by-step flow

1. Visitor opens `/categories` → index of categories + surfaced collections.
2. Or `/categories/{slug}` → `Category::findBySlug()`; 404 if hidden/unknown.
3. `Cookbook::inCategory()` loads all, then again with any filters.
4. Real difficulty levels and executable/preview presence are derived.
5. `categories/show` renders with only the filters that apply.

## Implementation map

- `app/Controllers/CategoryController.php` — `index()`, `show()`.
- `app/Models/Category.php`, `Cookbook.php`.
- `app/Services/CollectionResolver.php` — surfaced collections.

## Data used

- **Reads:** categories, cookbooks, and collection membership.
- **Writes:** none.

## Dependencies

The discovery taxonomy (`categories-collections-taxonomy`).

## Dependents

Links to **View a Cookbook detail** and **View a collection**.

## Failure cases

- Hidden or unknown category slug → 404.

## Current state

- **Status:** implemented. **Verification:** verified from source
  (`CategoryController` traced end to end).

## Use caution

Reads go through `primary_category_id`, never the legacy `cookbooks.category`
string — see the `legacy-category-column` warning.

## Related functionality

- View a collection
- View a Cookbook detail
