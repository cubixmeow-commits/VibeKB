---
id: php8-shared-hosting
type: constraint
title: Plain PHP 8 on shared hosting — no Node, Composer, or workers
summary: SousMeow must run on Hostinger shared hosting with PHP 8 and no build step, no dependency manager, no Docker, and no background workers.
status: active
verification: verified-from-source
updated: 2026-07-16
functionality: [route-and-secure-requests, seed-and-sync-content]
files: [docs/DEPLOYMENT.md, README.md]
tags: [hosting, deployment]
---

## Constraint

Runs on PHP 8.1+ on ordinary Hostinger shared hosting. No Node, no Composer, no
Docker, no background workers. Requires `pdo_sqlite`/`pdo_mysql`, `mbstring`,
and `zip` extensions.

## Source

The deployment target (`docs/DEPLOYMENT.md`, `README.md`).

## Affected functionality

Every feature. There is no build step; what is uploaded is what runs. Scheduled
work (the daily simulation) is a cron-invoked CLI script, not a resident worker.

## Consequences

- All front-end enhancement is progressive; the app works without JavaScript.
- No autoloader from Composer — the app ships its own bootstrap.

## Still active?

Yes. This shapes every implementation choice.
