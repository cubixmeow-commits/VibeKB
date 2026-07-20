---
id: system-deployment
type: system
title: Deployment
summary: Plain PHP 8 on Hostinger shared hosting; only public/ is web-served, config/storage sit above it, and a CLI seed sets up the catalog and admin.
updated: 2026-07-16
verification: verified-from-source
---

## How it ships

SousMeow is plain PHP 8 plus MySQL or SQLite — no Node, no Composer, no
workers. The `projects/sousmeow` folder is uploaded and the domain document root
points at `sousmeow/public`. `app/`, `config/`, `database/`, `scripts/`, and
`storage/` must sit **above** the document root. `public/.htaccess` routes clean
URLs through `index.php` (mod_rewrite).

## Configure

Copy `config/config.example.php` to `config/config.php` (gitignored) and set:
`app.env = 'production'`, `app.base_url`, `app.base_path` (if under a
subdirectory), `session.secure = true`, and the DB driver (SQLite file in
`storage/`, or MySQL from hPanel). `.env` can override sensitive values;
environment variables take precedence.

## Seed

`php scripts/seed.php --admin-email you@domain` applies the schema, syncs the
catalog (upsert by slug — safe to re-run after deploys), and prints the admin's
temporary password once. `--status` reports catalog health; `--fresh` wipes all
data; `--reset-password` rotates a password. The script is CLI-only (404 over
HTTP).

## Subfolder-aware

The front controller strips `app.base_path`, so the app runs at the domain root
or under a subdirectory (e.g. `/iain/projects/sousmeow/public`). Email links use
`APP_URL` + `APP_BASE_PATH`; a mismatch causes "page not found" links
(`scripts/print-url-config.php` diagnoses it).

## The constraints behind this

Everything here follows from `php8-shared-hosting`, `public-root-subfolder`,
`sqlite-and-mysql`, and `cli-only-admin`. Requires PHP 8.1+ with
`pdo_sqlite`/`pdo_mysql`, `mbstring`, and `zip`.
