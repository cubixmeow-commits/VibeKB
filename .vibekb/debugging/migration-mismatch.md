---
title: Migration mismatch after deploy
summary: Forms fail or pages error after deploy when production SQLite was not migrated to match the new code.
category: database
updated: 2026-07-15
order: 2
---

## Check in order

1. Compare migration files in the repo with what was applied on the server.
2. Inspect the live table schema.
3. Reproduce create and edit once on production.
4. Restore from backup before attempting speculative ALTER statements.

## Prevention

Treat migration application as part of deploy, not as an optional follow-up.
