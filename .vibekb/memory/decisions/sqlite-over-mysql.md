---
id: sqlite-over-mysql
type: decision
title: SQLite instead of MySQL
summary: Persist ideas in a single SQLite file rather than a MySQL/MariaDB service, because one operator and one host make a local file simpler to deploy and back up.
status: accepted
verification: reported-by-developer
created: 2026-01-14
updated: 2026-07-10
functionality: [create-idea, browse-ideas, initialize-database]
files: [src/Database.php]
tags: [database, architecture]
---

## Context

A single-operator app on cPanel shared hosting needs durable storage. The
choice was between a managed MySQL database and a local SQLite file.

## Decision

Use SQLite. The whole database is one file beside the app.

## Alternatives considered

- **MySQL/MariaDB** — rejected for now: extra service to provision, credentials
  to manage, and no benefit at single-writer volume.
- **Flat JSON files** — rejected: no transactions, awkward querying and
  ordering.

## Reason

- Single-writer workload needs no concurrency scaling.
- Deployment stays trivial: no database server to configure.
- Backup is copying one file.
- Local development matches production closely.

## Consequences

- Concurrent writers are not a design goal.
- Moving to multi-user hosting later may require a different engine.
- The SQLite file's permissions and location become a deploy checklist item.

## Current status

Active. Do not reverse mid-feature because an agent suggests "production
readiness". Pair any engine change with migration notes and a backup plan.
