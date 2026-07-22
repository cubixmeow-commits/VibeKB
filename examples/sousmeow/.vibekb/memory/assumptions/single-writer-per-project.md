---
id: single-writer-per-project
type: assumption
title: One writer per project at a time
summary: A project is owned and edited by one user in one place; there is no concurrent-editing model, and SQLite WAL/busy-timeout cover the low contention.
status: active
confidence: high
verification: verified-from-source
updated: 2026-07-16
functionality: [fill-pantry, paste-response, approve-and-version]
invalidated_by: Any shared or collaborative project editing, or high write concurrency on SQLite.
next_check: Revisit if collaboration or team projects are introduced.
tags: [concurrency, scope]
---

## Claim

Each project belongs to one user (`projects.user_id`, ownership enforced at the
data layer) and is worked on by that one person. There is no locking or
last-write-wins conflict handling because concurrent writers are not a design
goal.

## Confidence

High — ownership scoping is source-verified, and SQLite is configured with WAL
and a busy timeout for the light contention that a single user creates.

## Affected functionality

Pantry saves, artifact versioning, and approval all assume a single writer.

## What would invalidate it

Collaborative editing, or moving to a workload with many concurrent writers per
project.

## Next verification action

None unless collaboration is added.
