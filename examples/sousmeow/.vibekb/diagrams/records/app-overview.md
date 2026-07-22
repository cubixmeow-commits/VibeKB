---
id: app-overview
type: diagram
title: SousMeow at a glance
summary: The whole system on one page — browser, PHP front controller, the auth/CSRF gate, controllers, the single PDO data path, and the deliberate absence of any AI call.
diagram_type: application-overview
group: whole-app
svg: app-overview.svg
functionality: [route-and-secure-requests, run-recipe, access-database]
files: [public/index.php]
data: [SQLite (dev), MySQL (prod)]
warnings: []
diagrams: [run-recipe-flow, request-flow, storage-map]
status: implemented
verification: verified-from-source
provenance: Reflects the verified request/routing and data-access records and the "never calls AI" decision. Source evidence — public/index.php, .vibekb/system/mental-model.md, decision:never-calls-ai.
last_verified: 2026-07-16
uncertainty: High-level map only; feature-level detail lives in the other diagrams and the functionality records.
created: 2026-07-21
updated: 2026-07-21
---

## What am I looking at?

A one-screen mental model of SousMeow. A user's browser sends requests to a
single PHP front controller (`public/index.php`), which routes them through an
authentication and CSRF gate to the controllers (discovery, the Runner, export,
and CLI-only admin). Everything persists through **one** PDO data path that
targets SQLite in development and MySQL in production.

The dashed red box is a **non-goal made explicit**: SousMeow never contacts an
AI provider. Users run prompts in their own AI and paste results back.

## Why it matters

This is the fastest way to orient before opening any functionality record — it
shows how the pieces fit without reading the whole codebase.

## What is uncertain

Only the high-level shape is shown here. The Router internals and some model
queries are still `inferred-from-source` (see the request-flow diagram).
