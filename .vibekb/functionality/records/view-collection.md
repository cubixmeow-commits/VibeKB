---
id: view-collection
type: functionality
title: View a collection
area: explore-discovery
summary: Flexible merchandising views over the catalog — editorial, dynamic, or attribute-based — that appear only once they have enough members.
status: implemented
verification: verified-from-source
user_facing: true
trigger: A visitor opens "/collections/{slug}".
updated: 2026-07-16
tags: [discovery, collections, public]
files: [app/Controllers/CollectionController.php, app/Views/collections/show.php, app/Models/Collection.php, app/Services/CollectionResolver.php]
reads: [sousmeow_collections, sousmeow_cookbook_collections, cookbooks]
writes: []
config: []
depends_on: []
related_memory: [decision:categories-collections-taxonomy]
---

## In one sentence

A curated or computed shelf of Cookbooks that surfaces only when it clears its
minimum member count.

## Current behavior

`CollectionController::show($slug)` loads the collection and returns the shared
404 unless `CollectionResolver::isSurfaced()` passes (visible and at/over
`min_display_count`). Membership resolves in `CollectionResolver` by
`collection_type`: `editorial` (explicit rows), `dynamic` (computed, e.g.
`recently-added`), or `attribute` (derived, e.g. recipe count ≥ 6, or
`est_minutes` ≤ 30).

## Step-by-step flow

1. Visitor opens `/collections/{slug}`.
2. `Collection::findBySlug()` loads it.
3. `CollectionResolver::isSurfaced()` gates visibility → 404 if not surfaced.
4. `CollectionResolver::cookbooksFor()` resolves members by type.
5. `collections/show` renders the shelf.

## Implementation map

- `app/Controllers/CollectionController.php` — `show()`.
- `app/Services/CollectionResolver.php` — the single membership-resolution point.
- `app/Models/Collection.php`.

## Data used

- **Reads:** collections, membership rows, and cookbooks.
- **Writes:** none.

## Dependencies

The taxonomy decision (`categories-collections-taxonomy`).

## Dependents

Cross-linked from categories, cookbook detail, and the marketing home.

## Failure cases

- Hidden or under-populated collection → 404 (the honesty gate).

## Current state

- **Status:** implemented. **Verification:** verified from source
  (`CollectionController` + the resolver contract in `docs/ARCHITECTURE.md`).

## Why it works this way

Categories are the stable URL spine; collections are flexible many-to-many
views on top — see the `categories-collections-taxonomy` decision.

## Related functionality

- Browse by category
- View a Cookbook detail
