---
id: no-silent-migrations
type: warning
title: Never auto-run migrations on a page load
summary: Migrations must be run deliberately with bin/migrate.php; auto-migrating during a request causes silent schema drift and surprise writes in production.
severity: medium
status: active
verification: verified-from-source
created: 2026-02-22
updated: 2026-07-14
functionality: [initialize-database]
files: [bin/migrate.php, src/Database.php]
tags: [database, migrations, gotcha]
---

## Affected functionality

`initialize-database` and, through it, all data.

## What can go wrong

If `Database.php` were made to apply migrations on connect, a deploy could
alter the production schema the moment the first visitor loads a page — with no
operator awareness and no backup taken.

## Cause

Convenience: it is tempting to "just migrate on boot" so setup is one step.

## What not to do

Do not move migration logic into the connection path or any controller.

## Safe procedure

Keep migrations in `bin/migrate.php`, run by the operator after deploying code
and taking a backup.

## Verification steps

Confirm no code applies migrations outside `bin/migrate.php`.
