---
id: seed-and-sync-content
type: functionality
title: Seed & sync content
area: system-deployment
summary: A CLI script applies the schema, upserts the versioned Cookbook/category/collection catalog by slug, and provisions the admin — safe to re-run after every deploy.
status: implemented
verification: inferred-from-source
user_facing: false
trigger: An operator runs "php scripts/seed.php" (never over HTTP).
updated: 2026-07-16
tags: [system, seeding, migrations, cli]
files: [scripts/seed.php, database/schema.sqlite.sql, database/schema.mysql.sql, database/seeds/content.php, database/seeds/categories.php, database/seeds/collections.php, app/Core/Database.php]
reads: [cookbooks, recipes, sousmeow_categories, sousmeow_collections]
writes: [cookbooks, recipes, recipe_checks, pantry_fields, cookbook_stages, sousmeow_categories, sousmeow_collections, sousmeow_cookbook_collections, users]
config: [db.driver]
depends_on: [access-database]
related_memory: [decision:categories-collections-taxonomy, warning:legacy-category-column]
---

## In one sentence

One CLI command sets up and updates the whole catalog from versioned seed files,
upserting by slug so it is safe to re-run after any deploy.

## Current behavior

`php scripts/seed.php` applies the dialect-appropriate schema, then **syncs**
the catalog: Cookbooks, categories, and collections are versioned, slug-keyed
seed files upserted-and-pruned. Unknown category slugs, unknown/non-editorial
collection slugs, and off-allowlist accents abort the seed loudly before any
write. It prints a one-time admin password. `--fresh` wipes all data;
`--status` prints catalog health; `--reset-password` rotates a password. The
script is CLI-only — requesting it over HTTP returns 404 because it lives
outside `public/`.

## Step-by-step flow

1. Operator runs `php scripts/seed.php` from the `sousmeow` directory.
2. The schema for the configured driver is applied (idempotent).
3. Categories, collections, then Cookbooks are validated and upserted by slug.
4. Removed items are pruned; the legacy `category` string is re-derived (kept
   one release for rollback).
5. A temporary admin password is printed once.

## Implementation map

- `scripts/seed.php` — the runner (read via README/DEPLOYMENT, not line-traced).
- `database/schema.*.sql` — the two dialects.
- `database/seeds/*` — the versioned catalog content.

## Data used

- **Writes:** the whole catalog plus the admin `users` row.

## Current state

- **Status:** implemented. **Verification:** inferred-from-source — behaviour is
  documented in `README.md` and `docs/DEPLOYMENT.md` and matches the schema and
  the taxonomy resolver; `scripts/seed.php` itself was not opened line by line.
  A future pass should trace it directly.

## Use caution

`--fresh` destroys all projects and exports. Migrations are forward and additive
(see `docs/ARCHITECTURE.md` on the SQLite FK caveat). The legacy `category`
column is written but never read — see the `legacy-category-column` warning.

## Why it works this way

Versioned, Git-diffable seed files with upsert-by-slug make the catalog
reproducible and deploy-safe.

## Related functionality

- Access the database
- Browse by category
