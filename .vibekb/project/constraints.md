---
id: project-constraints
type: project
title: Constraints overview
summary: The hard boundaries the software is built inside. Each links to a constraint record with the detail.
updated: 2026-07-10
---

## The boundaries this app is built inside

These are not preferences — they shape how every feature must be implemented.
The full detail for each lives in the repository-memory constraint records
under **Why it works this way**.

- **PHP 8.2 on cPanel shared hosting, deployable in a subfolder.** No Node in
  production, no build step. (`php82-cpanel-subfolder`)
- **Single operator, no authentication.** Every request is trusted as the one
  owner. (`single-user-no-auth`)
- **SQLite only.** One local database file; no MySQL/Postgres service.
  (`sqlite-only`)
