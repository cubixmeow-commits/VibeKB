---
id: system-components
type: system
title: Major components
summary: A custom PHP MVC — front controller, Router, Core services, Controllers, Models, Services, and server-rendered Views — with one PDO data path.
updated: 2026-07-16
verification: verified-from-source
---

## The parts and their jobs

| Component | Responsibility |
|-----------|----------------|
| `public/index.php` | Front controller: security headers, CSP, CSRF gate, dispatch |
| `app/Core/Router.php` | Route table and dispatch (`app/routes.php` defines routes) |
| `app/Core/` | Framework: `Auth`, `Csrf`, `Database`, `Config`, `Env`, `Session`, `Flash`, `RateLimiter`, `Mailer`, `View` |
| `app/Controllers/` | One class per area (Marketing, Marketplace, Category, Collection, Auth, Account, Kitchen, Project, Runner, Export, Admin, Legal, Philosophy) |
| `app/Models/` | Table access: `Cookbook`, `Recipe`, `PantryField`, `Project`, `Artifact`, `Category`, `Collection`, `Export`, `User`, `CookbookStage`, `PasswordReset` |
| `app/Services/` | Logic: `PromptBuilder`, `ResponseParser`, `OutputContract`, `ProjectKit`, `SafeText`, `CollectionResolver`, `SiteStats`, `Simulation*`, `Accent`, mailers |
| `app/Views/` | Server-rendered PHP templates under a shared `layout/app.php` |
| `database/`, `scripts/seed.php` | Two schema dialects, versioned seeds, the CLI seeder |

## Where behaviour lives

- **Prompt assembly** is only in `PromptBuilder`.
- **Review parsing** is only in `ResponseParser` / `OutputContract`.
- **SQL** lives in Models; **the connection** only in `Core/Database`.
- **CSRF** is one gate in the front controller; **auth** is `Core/Auth`.
- **Escaping** of untrusted (pasted) content is `Services/SafeText`.

Keeping these boundaries is what keeps the read and write paths aligned; when
they blur, the `read-write-path-coupling` warning becomes a real bug.
