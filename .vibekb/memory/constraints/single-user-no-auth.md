---
id: single-user-no-auth
type: constraint
title: Single operator, no authentication
summary: The app assumes exactly one trusted user and implements no login or per-user ownership.
status: active
verification: verified-from-source
created: 2026-01-14
updated: 2026-07-05
functionality: [create-idea, change-idea-status]
tags: [security, scope]
---

## Constraint

There is one operator. No authentication, sessions, or ownership checks exist.
Every request is treated as the owner.

## Source

Deliberate scope decision: the app is a personal tool. See the project intent.

## Affected functionality

All write actions (`create-idea`, `change-idea-status`) skip CSRF and auth
because there is no second user to protect against.

## Consequences

- The app must not be exposed as if it were multi-user.
- Adding a login page alone does **not** make it multi-user safe — see the
  `half-auth-not-multiuser` warning.

## Still active?

Yes. Changing this is a fundamental redesign, not a feature.
