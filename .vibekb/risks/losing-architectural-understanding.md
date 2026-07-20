---
title: Losing architectural understanding
summary: Features can accumulate faster than anyone can explain them. Without a maintained mental model, later changes become guesswork.
severity: high
status: active
updated: 2026-07-18
order: 1
---

## Why this is the primary risk

SaaS Idea Manager is small enough to feel obvious today. That feeling decays as agents add statuses, filters, notes fields, and “quick” helpers. The code may still run while the owner can no longer say what is safe to change.

## Signals

- You cannot explain the request path without opening several files.
- Schema columns exist that no template displays.
- Two different write paths update ideas with different validation rules.

## Mitigation

Keep this publication current in the same work that changes behavior. Prefer deleting unused paths over leaving “maybe useful later” code undocumented.
