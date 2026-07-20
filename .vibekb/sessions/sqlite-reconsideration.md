---
title: Session — SQLite vs PostgreSQL reconsideration
summary: Month 2 conversation about switching databases; decision to stay on SQLite was recorded with full reasoning.
date: 2026-03-08
order: 2
---

## What triggered the discussion

AI suggested PostgreSQL for “production readiness” when planning a future export feature.

## Options on the table

- **PostgreSQL** — better if multi-user arrives
- **MySQL on cPanel** — familiar to host, more moving parts
- **Stay on SQLite** — still matches single-operator assumption

## Outcome

Stayed on SQLite. Recorded preconditions for any future engine change: real multi-user need, migration plan, backup strategy.

## Why this session mattered

Without capture, the next agent would have suggested the same switch again.
