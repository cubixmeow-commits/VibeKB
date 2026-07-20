---
title: AI session start — read this first
summary: Context every new Cursor or Claude session should load before editing this repository.
date: 2026-07-01
order: 1
---

## Before you change code

1. Read Project Guide chapter 7 (AI context) and chapter 6 (developer warnings)
2. Check `decisions/` for boundaries you must not cross casually
3. If touching schema, read `warnings/no-silent-migrations.md`

## Assumptions still in force

- Single operator, no auth
- SQLite file beside the app
- cPanel deploy; database file is not rsynced

## What to update after your session

- Relevant guide chapter if behavior or intent changed
- `decisions/` if you chose or rejected an approach
- `debugging/` if you solved a new failure mode

## Do not

- Add authentication “because production”
- Switch database engines without a recorded decision
- Generate documentation-only essays—capture reasoning that changes decisions
