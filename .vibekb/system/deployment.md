---
id: deployment
type: system
title: Deployment
summary: Mode A deploys as plain PHP to cPanel shared hosting via `.cpanel.yml` (no build, no DB, no rewrite rules, subfolder-safe); Mode B publishes `/docs` to GitHub Pages or any static host with relative links.
updated: 2026-07-22
---

## Mode A — the dynamic guide on shared hosting

`.cpanel.yml` rsyncs the runtime paths (`index.php`, `assets/`, `guide/`,
`.vibekb/`) into a cPanel public folder and excludes development-only paths
(`tools/`, `prompts/`, `docs/`, authoring docs, `examples/`). Query-string
routing means no `.htaccess` rewrite rules are needed and the app runs in a
subfolder. `.vibekb/` **must** deploy — it is the content.

## Mode B — the static snapshot

`php tools/generate-static.php` builds `/docs`; publish it via GitHub Pages
(Settings → Pages → branch → `/docs`) or any static host. Relative links make it
work at a web root or under a repository subpath. No PHP, database, CDN, or
network required.

## Deployment is part of the application

Whenever repository structure, runtime folders, or deployment requirements change,
`.cpanel.yml` and `DEPLOYMENT.md` are updated in the same change. Secrets never
enter the deploy sync.
