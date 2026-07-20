---
title: Manual database migrations
summary: Schema changes are written as SQL files and applied by hand on each environment. There is no automatic migration runner.
status: accepted
date: 2026-04-01
updated: 2026-07-15
order: 3
---

## Decision

Keep database migrations manual: author SQL, apply it on the server, confirm the app still works.

## Why

- The schema is small.
- Automatic runners add failure modes on shared hosting.
- Manual steps force a human checkpoint before irreversible changes.

## Consequences

- Staging and production can drift if someone forgets to apply a file.
- Agents must be told to create migration SQL and to update this publication when schema meaning changes.
- “It works on my machine” often means the local SQLite file already has columns production lacks.

## Practice

When adding a field: migration SQL, write path, read path, templates, then update Risks and Debugging if the new field can be null or missing.
