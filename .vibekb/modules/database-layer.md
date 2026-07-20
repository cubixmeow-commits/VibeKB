---
title: Database layer
summary: SQLite connection bootstrap, prepared statements, and the schema/migration files that define idea storage.
path: bootstrap and database helpers
updated: 2026-07-15
order: 2
---

## Responsibility

Provide a reliable connection and a clear schema story for idea data.

## Contains

- Connection setup
- Helpers for prepared queries
- Migration SQL files
- Guidance for file permissions on the database path

## Watchouts

Permission errors and schema drift show up here first. Keep migrations documented when columns change meaning.
