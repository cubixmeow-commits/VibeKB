---
title: New operator onboarding
summary: Human handoff: where to start when you inherit this project.
date: 2026-07-05
order: 2
---

## Start here (30 minutes)

1. Project Guide chapter 1 — collective memory overview
2. Chapter 2 — timeline (how it grew)
3. Chapter 5 — decision history (what is not negotiable yet)

## Then run locally

- PHP 8.2+, SQLite extension
- Copy `data/ideas.sqlite.example` if provided; create schema via documented migration

## Files that matter most

- `includes/db.php` — connection and path assumptions
- `ideas/` — CRUD entry points
- `.vibekb/decisions/` — why the boring choices are boring on purpose

## When you are stuck

Use debugging guides before asking AI to rewrite working code.
