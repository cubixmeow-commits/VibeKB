---
id: taxonomy-categories-collections
type: change
title: Introduced the categories + collections taxonomy
summary: Discovery moved from a single free-text category string to a stable one-category spine plus flexible many-to-many collections, resolved in one service with an honesty gate.
status: shipped
verification: verified-from-source
updated: 2026-07-16
functionality: [browse-categories, view-collection, view-cookbook-detail, seed-and-sync-content]
files: [database/schema.sqlite.sql, database/schema.mysql.sql, app/Services/CollectionResolver.php, app/Models/Category.php, app/Models/Collection.php, docs/ARCHITECTURE.md]
tags: [taxonomy, discovery, migration]
---

## Before

Each Cookbook stored one free-text `category` string, used directly for display
and category-based search. There was no stable URL spine and no way to build
themed, cross-cutting shelves.

## After

- `sousmeow_categories`: a fixed vocabulary; every visible Cookbook has one
  `primary_category_id`. Categories own `/categories/{slug}`.
- `sousmeow_collections` + `sousmeow_cookbook_collections`: many-to-many
  merchandising, resolved in `CollectionResolver` by `collection_type`
  (editorial / dynamic / attribute), gated by `min_display_count`.

## What caused the change

The need for both a stable structure (URLs, breadcrumbs, admin sort) and
flexible discovery shelves — see the `categories-collections-taxonomy` decision.

## Data impact

New taxonomy tables (prefixed `sousmeow_` to avoid collisions on the shared
production database) and `cookbooks.primary_category_id`. The legacy
`cookbooks.category` column is kept one release for rollback and is written but
never read (`legacy-category-column`).

## Compatibility concerns

SQLite cannot `ALTER`-add the foreign key on an upgraded DB, so the FK is
MySQL-only on upgrades; `--fresh` rebuilds it. Documented in
`docs/ARCHITECTURE.md`.

## Verification performed

Verified from source: the schema, `CollectionResolver`, and the category/
collection controllers were traced.

## Related decisions

`categories-collections-taxonomy`.
