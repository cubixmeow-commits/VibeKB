---
id: system-components
type: system
title: Major components
summary: Controllers, domain service, repository, database, and templates — and what each is responsible for.
updated: 2026-07-10
---

## The parts and their jobs

| Component | Responsibility |
|-----------|----------------|
| `public/*.php` controllers | One per page/action: list, view, create, update status, export |
| `src/IdeaService.php` | Validation and application rules (valid idea, allowed statuses) |
| `src/IdeaRepository.php` | All SQL for the `ideas` table |
| `src/Database.php` | Opens the SQLite file, configures PDO, returns the shared connection |
| `migrations/*.sql` + `bin/migrate.php` | Explicit, ordered schema changes |
| `templates/*.php` | Server-rendered HTML partials |
| `src/config.php` | Reads `IDEAS_DB_PATH` and other environment configuration |

## Where behaviour lives

- **Validation** is only in the service — controllers do not duplicate it.
- **SQL** is only in the repository — controllers never write queries.
- **Connection details** are only in `Database.php` — nothing else opens the
  file.

Keeping these boundaries is what keeps the read and write paths aligned. When
they blur, the `read-write-path-drift` warning becomes a real bug.
