---
id: legacy-category-column
type: warning
title: cookbooks.category is legacy — written but never read
summary: The old free-text category string is kept one release for rollback safety; every read goes through primary_category_id. Do not reintroduce reads of it.
severity: low
status: active
verification: verified-from-source
updated: 2026-07-16
functionality: [browse-categories, seed-and-sync-content, view-cookbook-detail]
files: [database/schema.sqlite.sql, database/schema.mysql.sql, docs/ARCHITECTURE.md]
tags: [database, migration, gotcha]
---

## Affected functionality

Category discovery and seeding.

## What can go wrong

Before the taxonomy change, each Cookbook had a single free-text `category`
string used for display and search. It is kept for one transitional release
(populated by the sync from the resolved category name) purely for rollback
safety. Reading it again would bypass the `categories`/`primary_category_id`
relation and reintroduce the inconsistency the taxonomy replaced.

## Cause

A deliberate, temporary rollback hedge documented in `docs/ARCHITECTURE.md`.

## What not to do

Do not add any application query or search that reads `cookbooks.category`.

## Safe procedure

The planned follow-up: confirm no query references it, drop the column in both
dialects (guarded `DROP COLUMN` in the seed migrate path), and remove the
derived-name write in `sync_content()`.

## Verification steps

`grep` the codebase for `cookbooks.category` reads; there should be none.
