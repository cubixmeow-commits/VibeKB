---
id: system-mental-model
type: system
title: The simplest mental model
summary: Browser to PHP to SQLite and back. One request, one file, one operator.
updated: 2026-07-10
---

## Hold this picture in your head

```
Browser  →  PHP controller  →  Repository  →  SQLite file  →  back to Browser
```

Every page is one PHP script. It reads or writes the single SQLite file through
a thin repository, renders a server-side template, and returns HTML. There is
no client-side app, no API layer, no background job, and no second server.

## The three layers

- **Controllers** (`public/*.php`) — one file per page/action. They read input,
  call a service or repository, and render a template.
- **Domain** (`src/IdeaService.php`) — the rules: what makes a valid idea, the
  allowed status lifecycle.
- **Data** (`src/IdeaRepository.php`, `src/Database.php`) — the SQL and the
  SQLite connection.

## What this means for change

If you change what an idea *is*, you touch all three layers plus a migration.
If you change how a page *looks*, you touch only a template. Knowing which case
you are in is most of the job.
