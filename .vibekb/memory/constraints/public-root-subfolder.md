---
id: public-root-subfolder
type: constraint
title: Only public/ is web-served; deployable under a subfolder
summary: The document root points at public/; app, config, database, scripts, and storage sit above it, and the app must run under a subdirectory via app.base_path.
status: active
verification: verified-from-source
updated: 2026-07-16
functionality: [route-and-secure-requests, export-project-kit]
files: [public/index.php, public/.htaccess, docs/DEPLOYMENT.md, config/config.example.php]
tags: [deployment, security, subfolder]
---

## Constraint

Web-serve `public/` only. `app/`, `config/`, `database/`, `scripts/`, and
`storage/` must sit above the document root. The app must also run under a
subdirectory (e.g. `/iain/projects/sousmeow/public`) via `app.base_path`.

## Source

`docs/DEPLOYMENT.md`, `public/index.php` (base-path stripping).

## Affected functionality

Routing (`app.base_path` is stripped before dispatch), export storage (zips in
`storage/exports/`, outside the web root), the SQLite file (in `storage/`), and
email links (`APP_URL` + `APP_BASE_PATH`).

## Consequences

- Secrets and data cannot be fetched over HTTP because they live above the root.
- Email links break if the base path is misconfigured
  (`scripts/print-url-config.php` diagnoses it).

## Still active?

Yes.
