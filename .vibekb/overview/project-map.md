---
title: Project Map
summary: A practical map of the SaaS Idea Manager tree: entry scripts, templates, database access, and where schema changes belong.
updated: 2026-07-18
order: 3
---

## Top-level shape

Think of the application as four cooperating areas:

1. **Entry scripts** — public PHP files that handle list, create, edit, and delete flows.
2. **Bootstrap and database** — shared connection setup and helpers for prepared statements.
3. **Templates** — HTML views for lists, forms, and empty states.
4. **SQLite file and migrations** — the durable store plus numbered SQL change scripts applied by hand.

## Where to look first

| Need | Start here |
| --- | --- |
| Change how ideas are listed | Ideas list entry script + list template |
| Add a field to an idea | Migration SQL, write path, form template, list/detail display |
| Fix a blank page after deploy | Bootstrap paths, SQLite file permissions, PHP error log |
| Understand storage | Database helper + schema/migration files |

## Migration discipline

Schema changes are manual. Add a new SQL migration file, apply it on the server, and verify the application still reads and writes. Do not invent an automatic migrator in an urgent bugfix unless you are prepared to document and own it.

## Publication note

This map describes the application under explanation, not the VibeKB publication engine that renders this website. Keep those two trees distinct in your mental model.
