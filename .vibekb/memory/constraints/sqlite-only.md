---
id: sqlite-only
type: constraint
title: SQLite only
summary: Storage is a single SQLite file; no external database service is available or assumed.
status: active
verification: reported-by-developer
created: 2026-01-14
updated: 2026-07-05
functionality: [initialize-database]
tags: [database]
---

## Constraint

The only persistence is one SQLite file. No MySQL/Postgres service is
provisioned.

## Source

Follows from the `sqlite-over-mysql` decision and the hosting setup.

## Affected functionality

`initialize-database` and every feature that reads or writes ideas.

## Consequences

- No cross-process concurrent-write scaling.
- Backups are file copies.

## Still active?

Yes, for as long as the app stays single-user.
