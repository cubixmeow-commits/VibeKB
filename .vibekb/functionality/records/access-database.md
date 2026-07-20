---
id: access-database
type: functionality
title: Access the database
area: system-deployment
summary: A single shared PDO handle — SQLite for dev, MySQL for production — is the only database access path; every query is a prepared statement.
status: implemented
verification: verified-from-source
user_facing: false
trigger: The first database access in a request.
updated: 2026-07-16
tags: [system, database, pdo]
files: [app/Core/Database.php, app/Core/Config.php]
reads: []
writes: []
config: [db.driver, db.sqlite_path, db.host, db.name, db.user, db.password]
depends_on: []
related_memory: [decision:single-pdo-path, constraint:sqlite-and-mysql]
---

## In one sentence

Every query in the app runs through one PDO handle with prepared statements —
SQLite locally, MySQL in production — and nothing else touches the database.

## Current behavior

`Database::pdo()` lazily builds one connection: MySQL when `db.driver` is
`mysql` (emulated prepares off), otherwise SQLite (creating the file/dir,
enabling `foreign_keys`, `busy_timeout`, and WAL). `run/fetch/fetchAll/
fetchValue/lastInsertId` wrap prepared statements; `transaction()` runs a
closure in a transaction, reusing an outer one if present. Errors throw
(`ERRMODE_EXCEPTION`).

## Step-by-step flow

1. Code calls `Database::run()` / `fetch()` / etc.
2. `pdo()` returns the shared handle (built once).
3. The SQL is prepared and executed with bound params.
4. Results return as associative arrays.

## Implementation map

- `app/Core/Database.php` — connection, query helpers, transactions.
- `app/Core/Config.php` — driver and credentials.

## Data used

- The single access point for all tables; no data of its own.

## Failure cases

- Bad credentials / unwritable SQLite dir → PDO exception (500 in production).

## Configuration

`db.driver` and the SQLite path or MySQL host/name/user/password.

## Current state

- **Status:** implemented. **Verification:** verified from source
  (`Database.php` traced in full).

## Use caution

There must be exactly one access path. Never interpolate values into SQL —
always bind. Both schema dialects must stay in lockstep.

## Why it works this way

A single prepared-statement path is the backbone of the security posture — see
the `single-pdo-path` decision and the `sqlite-and-mysql` constraint.

## Related functionality

- Route & secure every request
- Seed & sync content
