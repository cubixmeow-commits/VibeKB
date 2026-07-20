---
id: project-intent
type: intent
title: Why the SaaS Idea Manager exists
summary: The intent is speed of capture and a durable, prioritised view of ideas — not a full project-management tool.
updated: 2026-07-10
functionality: [create-idea, browse-ideas]
---

## Outcome it must produce

The operator should be able to capture an idea faster than they could open a
notes app, and later see all ideas in one place ranked by priority. The value
is that no idea is lost and the most important ones are obvious.

## Problem it addresses

Ideas arrive at random moments and get scattered across chats, notes, and
memory. By the time the operator wants to act, half of them are gone and the
rest have no relative priority.

## What it must not become

- A team collaboration tool. The moment it needs accounts, the design changes
  fundamentally (see the `single-user-no-auth` constraint and the
  `half-auth-not-multiuser` warning).
- A generic CRM or kanban board. Scope creep here would trade the core promise
  (fast capture, clear priority) for feature breadth.
