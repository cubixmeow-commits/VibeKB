---
id: system-storage
type: system
title: Storage
summary: One SQLite file holds every idea; a migrations table tracks schema versions. No other persistence.
updated: 2026-07-16
---

## What is stored, and where

Everything lives in a single SQLite file selected by `IDEAS_DB_PATH`
(default `data/ideas.sqlite`). There is no session store, no cache, and no
external storage.

## The `ideas` table

| Column | Meaning | Written by | Read by |
|--------|---------|------------|---------|
| `id` | Primary key | database | View, Change status |
| `title` | Short idea name | Create | Browse, View, Export |
| `notes` | Free-text detail | Create | View, Export |
| `status` | Lifecycle value (`inbox` … `parked`) | Create, Change status | Browse, View, Export |
| `priority` | Integer sort key (lower = higher priority) | Create | Browse |
| `created_at` | Creation timestamp | Create | Browse, View, Export |
| `updated_at` | Last change timestamp | Create, Change status | View |

## The `schema_migrations` table

Records which numbered migration files have been applied so `bin/migrate.php`
never re-runs one. This is the source of truth for "what schema version is this
database".

## Durability and backup

Because storage is one file, backup is copying that file. It is **not** part of
the git deploy (it is excluded so a deploy can never overwrite live data). The
operator is responsible for backups.

## Sensitive data

None by design — ideas are the operator's own notes, and there are no
credentials stored. Adding anything sensitive would reopen the
`half-auth-not-multiuser` concern.
