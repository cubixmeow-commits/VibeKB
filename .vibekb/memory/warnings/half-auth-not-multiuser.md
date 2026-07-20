---
id: half-auth-not-multiuser
type: warning
title: A login page alone does not make this multi-user
summary: Adding authentication without per-record ownership would create the illusion of multi-user safety while every user still sees and edits every idea.
severity: high
status: active
verification: verified-from-source
created: 2026-04-02
updated: 2026-07-05
functionality: [create-idea, change-idea-status, view-idea]
tags: [security, scope]
---

## Affected functionality

All idea reads and writes.

## What can go wrong

Someone (or an AI session chasing "production readiness") adds a login page.
The app now feels multi-user — but there is no `owner` on ideas and no
ownership check, so every logged-in user sees and edits everyone's ideas.

## Cause

The `single-user-no-auth` constraint means ownership was never modelled.
Authentication and authorization are separate problems; adding only the former
is worse than neither, because it hides the gap.

## What not to do

Do not add login as a "quick win". Do not expose the app to multiple users on
the strength of a login page.

## Safe procedure

If multi-user is genuinely needed: model `owner` on ideas, add ownership
checks to every read and write, add CSRF protection, and reconsider SQLite
under concurrent writers — as one deliberate project, not an incremental patch.

## Verification steps

Confirm no code path treats the app as multi-user until ownership exists.
