---
id: route-and-secure-requests
type: functionality
title: Route & secure every request
area: system-deployment
summary: The front controller — security headers and CSP, then bootstrap, then one CSRF gate for all POSTs, then routing, with a subfolder-aware base path.
status: implemented
verification: verified-from-source
user_facing: false
trigger: Every request that is not a real file under public/.
updated: 2026-07-16
tags: [system, routing, security, csrf]
files: [public/index.php, public/.htaccess, app/bootstrap.php, app/Core/Router.php, app/Core/Csrf.php, app/routes.php]
reads: []
writes: []
config: [app.env, app.base_path]
depends_on: []
related_memory: [decision:front-controller-csrf-gate, constraint:public-root-subfolder]
---

## In one sentence

One entry point sets security headers, verifies CSRF on every POST, and
dispatches to a route — and it works from a subfolder.

## Current behavior

`public/index.php` sends `X-Content-Type-Options`, `X-Frame-Options`,
`Referrer-Policy`, and a CSP (`script-src 'self'`; inline styles allowed for
server-computed geometry). It bootstraps, installs an exception handler (full
detail only in development), strips `app.base_path` from the path, calls
`Csrf::verify()` for **every** POST before routing, then dispatches through the
`Router`. On Apache, `public/.htaccess` routes clean URLs here; the built-in dev
server hands back real files.

## Step-by-step flow

1. Request hits `public/index.php` (real files are served directly).
2. Security headers + CSP are sent.
3. `bootstrap.php` loads config and the autoloader.
4. `app.base_path` is stripped so the app works under a subdirectory.
5. POST → `Csrf::verify()` (single gate).
6. `Router::dispatch($method, $path)` invokes the controller.

## Implementation map

- `public/index.php` — the front controller.
- `app/Core/Router.php` — route table + dispatch (404/405).
- `app/Core/Csrf.php` — the single CSRF gate.
- `app/routes.php` — the route definitions.

## Data used

- None directly; it wires the request to a controller.

## Failure cases

- Failed CSRF → the request is rejected before routing (419 view).
- Unknown route → 404; wrong method → 405.
- Uncaught exception → logged; 500 view in production, detail in development.

## Configuration

`app.env` (dev shows errors), `app.base_path` (subdirectory prefix).

## Current state

- **Status:** implemented. **Verification:** verified from source
  (`public/index.php` traced; `Router`/`Csrf` read at the call boundary).

## Use caution

The single CSRF gate covers every POST — do not add a POST route that bypasses
the front controller. Keep `script-src 'self'` in the CSP.

## Why it works this way

One gate in one place is easier to reason about than per-controller checks — see
the `front-controller-csrf-gate` decision.

## Related functionality

- Access the database
- Seed & sync content
