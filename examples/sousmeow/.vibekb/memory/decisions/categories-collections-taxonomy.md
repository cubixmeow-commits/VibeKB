---
id: categories-collections-taxonomy
type: decision
title: Categories are the stable spine; Collections are flexible views
summary: Each Cookbook has exactly one primary category (the URL spine) and belongs to many collections (merchandising), resolved in one place with an honesty gate.
status: accepted
verification: verified-from-source
updated: 2026-07-16
functionality: [browse-categories, view-collection, view-cookbook-detail, seed-and-sync-content]
files: [app/Services/CollectionResolver.php, database/schema.sqlite.sql, docs/ARCHITECTURE.md]
tags: [architecture, discovery, taxonomy]
---

## Context

Discovery needs both a stable structure (URLs, breadcrumbs, admin sort) and
flexible merchandising (themed shelves).

## Decision

- **Categories**: one per publicly visible Cookbook (`primary_category_id`,
  `ON DELETE SET NULL`); a fixed vocabulary owning the `/categories/{slug}` spine.
- **Collections**: many-to-many (`/collections/{slug}`) resolved by
  `CollectionResolver` via `collection_type` (`editorial`, `dynamic`,
  `attribute`). `min_display_count` gates surfacing so a thin shelf 404s.

## Alternatives considered

- **Many-to-many top-level categories** — rejected: no stable URL spine,
  breadcrumb, or sort; "where does this live?" becomes a per-Cookbook judgement.
- **A view-tracking/analytics table** — deferred: no counting policy and no
  surface needs it yet.

## Consequences

- The legacy `cookbooks.category` string is kept one release for rollback and is
  written but never read — see the `legacy-category-column` warning.
- SQLite upgrades get the category column and index without the FK (SQLite can't
  `ALTER`-add one); MySQL (production) gets the FK.

## Current status

Active. Membership resolves only in `CollectionResolver`.
