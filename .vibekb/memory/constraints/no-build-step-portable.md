---
id: no-build-step-portable
type: constraint
title: Runs on PHP 8.2 shared hosting with no build step
summary: No framework, SPA, bundler, database, external/AI API, or JavaScript build; the guide must run by copying files and work in a subfolder without JavaScript.
status: active
verification: verified-from-source
updated: 2026-07-22
functionality: [parse-records, render-guide, search-the-model, deploy-and-stay-portable]
files: [guide/lib/Markdown.php, guide/lib/UrlStrategy.php, .cpanel.yml]
tags: [portability, constraint]
---

## The constraint

The dynamic guide must run on ordinary PHP 8.2 shared hosting: no Composer
install, no Node/bundler, no SQL database, no external or AI API, no network at
render time, and no rewrite rules. It must work without JavaScript (JS only
enhances) and deploy into a subfolder.

## Where it bites

- The Markdown renderer is a hand-written subset, not a library, so there is no
  `composer install`.
- Routing is query-string based, so no `.htaccess` rewrites are required.
- The static snapshot uses relative links so it works under a repository subpath.
- Search runs client-side against a static JSON index — no server, no CDN.

## What not to do

Do not introduce a framework, SPA, bundler, SQL database, external/AI API, or a
required build step. Any AI analysis is performed by the coding agent already in
the repository — never by a service VibeKB calls at runtime.

## Consequences

This constraint is why VibeKB can be reviewed in a pull request and deployed by
copying files — and why the model must stay plain files, not a database.
