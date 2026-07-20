---
title: Future multi-user without ownership checks
summary: Adding accounts later without ownership validation on idea records would expose every idea to every logged-in user.
severity: high
status: active
updated: 2026-07-12
order: 2
---

## The trap

It is tempting to “just add login” and leave the ideas table unchanged. That creates an application that authenticates users and then shows everyone the same global idea list.

## What must change together

- A users concept or equivalent identity
- An ownership field on ideas
- Queries filtered by owner
- Create paths that stamp ownership
- Tests or manual checks proving isolation

## Guidance

Until those pieces land together, treat multi-user as out of scope. Do not half-ship authentication.
