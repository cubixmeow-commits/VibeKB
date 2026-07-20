---
title: SQLite instead of MySQL
summary: The app uses SQLite because one operator and one host make a local file database simpler to deploy and back up than a shared MySQL service.
status: accepted
date: 2026-03-02
updated: 2026-07-10
order: 1
---

## Decision

Persist idea records in SQLite rather than MySQL or MariaDB.

## Why

- Single-user workload does not need concurrent write scaling.
- cPanel deploys stay simpler when the database is a file beside the app.
- Backups reduce to copying one file with the rest of the site data.
- Local development matches production closely.

## Consequences

- Concurrent writers are not a design goal.
- Moving to multi-user hosting later may require a different engine or careful locking strategy.
- File permissions on the SQLite database become a deploy checklist item.

## Do not reverse lightly

Switching to MySQL is fine when multi-user or concurrent access becomes real. Do not switch mid-feature because an agent suggests “production readiness.” Pair any engine change with migration notes and a backup plan.
