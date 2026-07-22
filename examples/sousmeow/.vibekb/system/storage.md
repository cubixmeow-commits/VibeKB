---
id: system-storage
type: system
title: Storage
summary: One relational database (SQLite dev / MySQL prod) holds the catalog and user work; export zips live on disk outside the web root. No other persistence.
updated: 2026-07-16
verification: verified-from-source
---

## What is stored, and where

All structured data is in one database — SQLite (`storage/sousmeow.sqlite`) in
dev or MySQL in production, same schema. Export zips are files in
`storage/exports/` (outside the web root). Sessions use PHP's session store.
There is no external storage.

## The catalog (seeded, read-mostly)

| Table | Meaning |
|-------|---------|
| `cookbooks` | Workflows: slug, status, `is_executable`, difficulty, category |
| `cookbook_stages`, `recipes` | Ordered steps, prompts, output contracts |
| `recipe_checks` | Per-recipe Quality Checks (+ evidence keys) |
| `pantry_fields` | Typed input definitions per Cookbook |
| `sousmeow_categories`, `sousmeow_collections`, `sousmeow_cookbook_collections` | Discovery taxonomy (prefixed to avoid collisions on shared hosting) |

## User work (written during a run)

| Table | Meaning | Written by |
|-------|---------|------------|
| `users` | Accounts, roles, verification, `simulation` flag | Auth/Account |
| `projects` | A user's run of a Cookbook | Start / Pantry / Runner |
| `pantry_values` | The facts entered (unique per project+field) | Fill the Pantry |
| `artifacts` | One per project+recipe; status + approved version | Runner |
| `artifact_versions` | Immutable, append-only (pasted/example/edited/restored) | Paste / Approve |
| `artifact_checks` | A confirmed check against a specific version | Review |
| `exports` | A built kit's filename, size, count | Export |
| `simulation_runs` | Daily demo-activity log | Simulation |

## Durability, backup, sensitivity

- Backup = the SQLite file or a MySQL dump; export zips are copied separately.
- Table names for the taxonomy carry a `sousmeow_` prefix because the
  production database is shared with other apps on the account.
- Passwords are hashed (`password_hash`); pasted content is the only untrusted
  bulk data and is sanitised/escaped.
