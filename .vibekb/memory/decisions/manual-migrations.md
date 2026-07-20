---
id: manual-migrations
type: decision
title: Manual, explicit migrations
summary: Schema changes are numbered SQL files applied on purpose by running a script — never automatically on a page load.
status: accepted
verification: verified-from-source
created: 2026-02-20
updated: 2026-07-14
functionality: [initialize-database]
files: [bin/migrate.php, migrations/001_create_ideas.sql, migrations/002_add_status.sql]
tags: [database, migrations, safety]
---

## Context

The schema will change as features are added. There needed to be a predictable
way to evolve it without surprising the operator or an AI session.

## Decision

Keep migrations as ordered, numbered SQL files. Apply them only by running
`bin/migrate.php`, which records applied versions in `schema_migrations`. The
app never migrates itself during a normal request.

## Alternatives considered

- **Auto-migrate on boot** — rejected: hides schema changes and can run
  unexpectedly in production. See the `no-silent-migrations` warning.
- **An ORM's migration framework** — rejected: too heavy for a single-file app
  under the `sqlite-only` constraint.

## Reason

The operator (and the next AI session) should always know exactly when the
schema changed, and be able to read the change as plain SQL.

## Consequences

- Deploying a schema change is a two-step operation: deploy code, then run the
  migration.
- Migrations are forward-only; there is no automatic rollback.

## Current status

Active and load-bearing for `initialize-database`.
