---
id: initialize-database
type: functionality
title: Initialize the database
area: deployment-operations
summary: Ensures the SQLite database file and schema exist, applying manual migrations on demand.
status: implemented
verification: verified-from-source
user_facing: false
trigger: The first database access in a request, or running the migrate script.
created: 2026-01-14
updated: 2026-07-14
tags: [database, system, migrations]
files: [src/Database.php, migrations/001_create_ideas.sql, migrations/002_add_status.sql, bin/migrate.php]
reads: [schema_migrations]
writes: [ideas, schema_migrations]
config: [IDEAS_DB_PATH]
depends_on: []
related_memory: [decision:sqlite-over-mysql, decision:manual-migrations, constraint:sqlite-only, warning:no-silent-migrations]
---

## In one sentence

The app makes sure the SQLite file exists and the schema is up to date before
any idea is read or written.

## User experience

Invisible to the operator in normal use. The operator experiences it only when
setting up the app or when a migration must be applied — a step they run
deliberately, not automatically.

## Current behavior

`src/Database.php` opens (or creates) the SQLite file at `IDEAS_DB_PATH` and
returns a shared PDO connection. Schema changes live as numbered SQL files in
`migrations/` and are applied by running `bin/migrate.php`, which records
applied versions in a `schema_migrations` table. The app does **not** silently
migrate on a normal page load.

## Step-by-step flow

1. Code requests a connection from `Database::connection()`.
2. If the SQLite file does not exist, PDO creates it.
3. For schema changes, the operator runs `php bin/migrate.php`.
4. The script reads `schema_migrations`, finds unapplied numbered files, and
   applies them in order inside a transaction.
5. Each applied file's version is recorded so it is never re-run.

## Implementation map

- `src/Database.php` — connection factory and PDO configuration.
- `migrations/*.sql` — ordered, manual schema changes.
- `bin/migrate.php` — the migration runner.

## Data used

- **Reads/Writes:** `schema_migrations` (tracking) and the `ideas` schema.

## Dependencies

None — this is the foundation every other functionality depends on.

## Dependents

**Create an idea**, **Browse ideas**, **View an idea**, **Change an idea's
status**, and **Export ideas** all require the initialised database.

## Failure cases

- Data directory not writable → SQLite cannot create the file; every feature
  fails with a generic error.
- A migration applied out of order or by hand → `schema_migrations` and the
  real schema diverge. See the `no-silent-migrations` warning.

## Configuration

`IDEAS_DB_PATH` — absolute path to the SQLite file. If unset, defaults to
`data/ideas.sqlite`.

## Current state

- **Status:** implemented.
- **Verification:** verified from source.
- **Limitations:** migrations are forward-only; there is no down/rollback.

## Safe to change

Adding a new **numbered** migration file is the safe way to evolve the schema.

## Use caution

Never edit an already-applied migration, and never let the app auto-run
migrations on a page load — both cause silent drift between code and data. See
the `no-silent-migrations` warning and `manual-migrations` decision.

## Why it works this way

SQLite keeps deployment to a single file (`sqlite-over-mysql`), and migrations
are manual and explicit so the operator always knows when the schema changed
(`manual-migrations`).

## Change history

- 2026-07-14 — `002_add_status.sql` added the `status` column
  (`added-status-field`).

## Current AI work

Not currently under active change.

## Related functionality

- Create an idea
- Change an idea's status
