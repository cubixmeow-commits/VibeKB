---
id: storage-map
type: diagram
title: Storage map — one PDO path, two dialects
summary: The tables SousMeow uses and the single PDO path that targets SQLite in development and MySQL in production, including the legacy category column that must never be read.
diagram_type: storage-map
group: technical-system
svg: storage-map.svg
functionality: [access-database, approve-and-version, fill-pantry]
files: []
data: [cookbooks, recipes, quality_checks, pantry_entries, artifacts, artifact_versions, projects, users]
warnings: [legacy-category-column, read-write-path-coupling]
diagrams: [app-overview, run-recipe-flow]
status: implemented
verification: verified-from-source
provenance: Reflects the single-PDO-path and immutable-artifact-versions decisions and the SQLite/MySQL constraint. Source evidence — decision:single-pdo-path, decision:immutable-artifact-versions, constraint:sqlite-and-mysql.
last_verified: 2026-07-16
uncertainty: Column-level detail is summarised; consult SCHEMA-level source for exact fields.
created: 2026-07-21
updated: 2026-07-21
---

## What am I looking at?

One PDO data path, chosen by environment (SQLite for development, MySQL for
production), with the schema kept compatible across both dialects. The main
tables are cookbooks/recipes, quality checks, pantry entries, artifacts and
their immutable versions, projects, and users.

The dashed red box is the **legacy `cookbooks.category` column** — it still
exists but must never be read; it was superseded by the categories/collections
taxonomy, and reading it reintroduces a resolved bug.

## Why it matters

It shows that there is exactly one data path (not per-dialect branches) and
that artifact versions are immutable — approving creates a new version rather
than editing in place.

## What is uncertain

Table set and relationships are verified; exact per-column definitions are
summarised here and should be read from source when precision matters.
