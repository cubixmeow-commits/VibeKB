---
id: request-flow
type: diagram
title: Request flow through the front controller
summary: How a request travels from the browser through the router, the auth and CSRF gate, a controller, the PDO data path, and back out as an escaped view — with CLI-only admin shown as a separate path.
diagram_type: request-flow
group: product-flows
svg: request-flow.svg
functionality: [route-and-secure-requests, access-database, view-admin-overview]
files: [public/index.php]
data: [SQLite (dev), MySQL (prod)]
warnings: []
diagrams: [app-overview]
status: implemented
verification: inferred-from-source
provenance: The web request path is verified-from-source (front-controller CSRF gate decision); the Router internals and some Model queries remain inferred-from-source. Source evidence — public/index.php, decision:front-controller-csrf-gate.
last_verified: 2026-07-16
uncertainty: The Router class itself has not been line-traced; treat the "Router match" step as inferred until confirmed.
created: 2026-07-21
updated: 2026-07-21
---

## What am I looking at?

Every web request enters `public/index.php`. The router matches it, an auth
check runs, and unsafe methods pass a CSRF gate before reaching a controller.
Controllers read and write through the single PDO data path and render an
**escaped** HTML view. Admin actions (seeding, management) are CLI-only and
bypass the web routes entirely — shown dashed.

## Why it matters

It shows the exact chokepoint where security is enforced. Anything that adds a
route or a state-changing action must go through this gate.

## What is uncertain

The Router matching step is `inferred-from-source` — the gate and escaping are
verified, but the Router class was not line-traced. Verify before relying on
routing edge cases.
