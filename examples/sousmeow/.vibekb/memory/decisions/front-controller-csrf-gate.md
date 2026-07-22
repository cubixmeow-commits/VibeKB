---
id: front-controller-csrf-gate
type: decision
title: One CSRF gate in the front controller
summary: Every POST is verified once in public/index.php before routing, rather than per-controller, alongside a strict same-origin CSP.
status: accepted
verification: verified-from-source
updated: 2026-07-16
functionality: [route-and-secure-requests]
files: [public/index.php, app/Core/Csrf.php]
tags: [architecture, security, csrf]
---

## Context

CSRF protection is easy to forget when it is a per-handler responsibility.

## Decision

`public/index.php` calls `Csrf::verify()` for **every** POST before the router
runs, and sends a CSP with `script-src 'self'` (inline styles allowed only for
server-computed geometry). Auth endpoints are additionally rate-limited.

## Reason

- A single choke point cannot be forgotten on a new route.
- Same-origin scripts blunt XSS even if some other escape were missed.

## Consequences

- Any POST must go through the front controller; a route that bypasses it loses
  CSRF protection.
- The CSP forbids third-party scripts (assets are self-hosted).

## Current status

Active. Do not add a POST path outside the front controller.
