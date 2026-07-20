---
id: project-constraints
type: project
title: Constraints overview
summary: PHP 8 on Hostinger shared hosting, public/ document root, MySQL or SQLite, no AI API, no Node/Composer/workers, CLI-only admin.
updated: 2026-07-16
verification: verified-from-source
---

## The boundaries SousMeow is built inside

These shape every implementation choice. Full detail is in the constraint
records under **Why it works this way**.

- **PHP 8, plain — no framework, no Composer, no Node, no Docker, no background
  workers.** (`php8-shared-hosting`) — verified from `docs/DEPLOYMENT.md`,
  `README.md`.
- **Only `public/` is web-served; `app/`, `config/`, `database/`, `scripts/`,
  and `storage/` sit above the document root.** Deployable under a subdirectory
  via `app.base_path`. (`public-root-subfolder`) — verified from
  `public/index.php`, `docs/DEPLOYMENT.md`.
- **One database, two dialects: SQLite (dev) and MySQL (production), same
  schema.** All access goes through one PDO handle in `app/Core/Database.php`.
  (`sqlite-and-mysql`) — verified from `app/Core/Database.php`, both schema files.
- **SousMeow never calls an AI.** No API keys, no token billing.
  (`never-calls-ai`) — verified from `README.md`, `PromptBuilder.php`.
- **Admin accounts and password rotation are CLI-only** (`scripts/seed.php`);
  there is no web installer. (`cli-only-admin`) — verified from `docs/DEPLOYMENT.md`,
  `AdminController.php`.
