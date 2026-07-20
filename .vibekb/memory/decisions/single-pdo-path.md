---
id: single-pdo-path
type: decision
title: One PDO connection, prepared statements only
summary: All database access goes through a single shared PDO handle in Core/Database with prepared statements; there is no other access path.
status: accepted
verification: verified-from-source
updated: 2026-07-16
functionality: [access-database]
files: [app/Core/Database.php]
tags: [architecture, security, database]
---

## Context

A small app on shared hosting needs a security posture that is easy to reason
about, across two database dialects.

## Decision

`app/Core/Database.php` owns the only connection and exposes
`run/fetch/fetchAll/fetchValue/lastInsertId/transaction`, all using prepared
statements. Every Model uses it; nothing else opens a connection.

## Reason

- One access path makes "is every query parameterised?" answerable by reading
  one file.
- Swapping SQLite (dev) for MySQL (production) is a config change, not a code
  change.

## Consequences

- Both schema dialects must be maintained in lockstep.
- No raw string-built SQL anywhere; values are always bound.

## Current status

Active and foundational to the security posture.
