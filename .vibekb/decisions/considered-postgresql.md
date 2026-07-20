---
title: Considered PostgreSQL, stayed on SQLite
summary: Month 2 database discussion; export plans did not justify an engine change yet.
status: accepted
date: 2026-03-08
updated: 2026-07-10
order: 5
---

## Context

AI suggested PostgreSQL when export and “production readiness” came up.

## Why we stayed

- Single-user assumption unchanged
- cPanel deploy stays one file to back up
- Export can be CSV from SQLite without a server migration

## When to revisit

Real multi-user need, concurrent writers, or hosted DB already provisioned—with migration notes and backup plan.

## Do not

Re-litigate this every session. Read this file first.
