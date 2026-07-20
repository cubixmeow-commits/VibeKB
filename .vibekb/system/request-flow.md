---
id: system-request-flow
type: system
title: The request lifecycle
summary: How a single HTTP request becomes a rendered page or a redirect.
updated: 2026-07-10
---

## One request, start to finish

1. The web server routes the URL to a single PHP file under `public/`.
2. `src/config.php` loads configuration (including `IDEAS_DB_PATH`).
3. The controller reads input from `$_GET` / `$_POST`.
4. For writes, `IdeaService` validates and applies rules.
5. `IdeaRepository` runs the SQL against the SQLite connection.
6. The controller either:
   - renders a `templates/*.php` partial (reads), or
   - sends a `303` redirect (writes) so a refresh does not repeat the write.

## Reads vs writes

- **Reads** (list, detail, export) end in a rendered response.
- **Writes** (create, update status) end in a redirect to a read page — the
  post/redirect/get pattern.

## Errors

Unexpected exceptions are caught at the entry point: details are logged and a
generic error page is shown. In development, full errors are shown instead —
controlled by configuration, never leaking internals in production.
