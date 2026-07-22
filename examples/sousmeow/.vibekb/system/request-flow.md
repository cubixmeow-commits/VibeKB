---
id: system-request-flow
type: system
title: The request lifecycle
summary: Every request enters the single front controller — headers, CSRF gate on POST, then routing to a controller that renders a view or redirects.
updated: 2026-07-16
verification: verified-from-source
---

## One request, start to finish

1. Apache (`public/.htaccess`) or the dev server sends the request to
   `public/index.php`; real asset files are served directly.
2. Security headers and the CSP are sent.
3. `app/bootstrap.php` loads config and the autoloader; an exception handler is
   installed (detailed errors only in development).
4. `app.base_path` is stripped so the app works from a subdirectory.
5. **Every POST** passes `Csrf::verify()` before routing.
6. `Router::dispatch()` matches the method+path and calls the controller.
7. The controller reads input, calls Models/Services, and either renders a
   `View` or issues a redirect (post/redirect/get for writes).

## Authentication in the flow

Controllers call `Auth::requireLogin()` / `requireVerified()` / `requireAdmin()`
as needed. Write actions in the Runner and Export require a verified account.
Ownership is enforced at the data layer (`findForUser`), and unknown ids return
404 without revealing other users' data.

## Errors

- Failed CSRF → 419. Unknown route → 404. Wrong method → 405.
- Uncaught exceptions are logged; production shows a generic 500 view, dev shows
  the trace.
