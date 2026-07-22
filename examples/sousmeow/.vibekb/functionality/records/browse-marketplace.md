---
id: browse-marketplace
type: functionality
title: Browse the marketplace
area: explore-discovery
summary: A single searchable listing of every Cookbook, filtered by a plain-text query.
status: implemented
verification: verified-from-source
user_facing: true
trigger: A visitor opens "/marketplace", optionally with "?q=".
updated: 2026-07-16
tags: [discovery, search, public]
files: [app/Controllers/MarketplaceController.php, app/Views/marketplace/index.php, app/Models/Cookbook.php]
reads: [cookbooks]
writes: []
config: []
depends_on: []
related_memory: []
---

## In one sentence

Type a query and see the matching Cookbooks; leave it blank to see them all.

## Current behavior

`MarketplaceController::index()` trims `?q=` and passes it to
`Cookbook::marketplace($query)`, then renders `marketplace/index`. There is no
pagination; the catalog is small and fully listed.

## Step-by-step flow

1. Visitor opens `/marketplace` (optionally `?q=cover+letter`).
2. `MarketplaceController::index()` reads and trims the query.
3. `Cookbook::marketplace($query)` returns matching Cookbooks.
4. `marketplace/index` renders the results.

## Implementation map

- `app/Controllers/MarketplaceController.php` — `index()`.
- `app/Models/Cookbook.php` — `marketplace()` query.
- `app/Views/marketplace/index.php` — the listing.

## Data used

- **Reads:** `cookbooks`. **Writes:** none.

## Dependencies

Seeded catalog content.

## Dependents

Feeds **View a Cookbook detail**.

## Failure cases

- No matches → an empty results state (not an error).

## Current state

- **Status:** implemented. **Verification:** verified from source (controller
  traced; the exact `marketplace()` SQL in `Cookbook.php` was not opened line by
  line).

## Safe to change

Result copy and query handling are safe to adjust.

## Related functionality

- View a Cookbook detail
- Browse by category
