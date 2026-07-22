---
id: sqlite-and-mysql
type: constraint
title: Two database dialects kept in lockstep
summary: The same schema must work as SQLite (zero-setup dev) and MySQL (production); both dialect files change together.
status: active
verification: verified-from-source
updated: 2026-07-16
functionality: [access-database, seed-and-sync-content]
files: [database/schema.sqlite.sql, database/schema.mysql.sql, app/Core/Database.php]
tags: [database, portability]
---

## Constraint

SousMeow supports SQLite (local, default) and MySQL (Hostinger). The two schema
files must stay identical in shape; `scripts/seed.php` picks the file for the
configured driver.

## Source

`app/Core/Database.php`, both `database/schema.*.sql`.

## Affected functionality

All data access, and seeding/migrations. A schema change must edit both files.

## Consequences

- Some migrate-path differences are unavoidable: SQLite cannot `ALTER`-add a
  foreign key, so an upgraded SQLite dev DB may lack an FK that MySQL has (see
  `docs/ARCHITECTURE.md`); `--fresh` rebuilds it.
- Timestamps are UTC `YYYY-MM-DD HH:MM:SS` strings in both.

## Still active?

Yes, as long as SQLite is the dev dialect and MySQL is production.
