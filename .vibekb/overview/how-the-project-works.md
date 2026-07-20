---
title: How the Project Works
summary: Requests hit PHP entry points, templates render forms and lists, and idea records persist in SQLite. There is no auth layer and no upload pipeline between the browser and the database.
updated: 2026-07-18
order: 2
---

## Request path

A browser request reaches a PHP script under the application root. That script loads a thin bootstrap, opens the SQLite connection, and either:

- renders a list or detail view, or
- accepts a form POST, validates required fields, writes to SQLite, and redirects back to a safe GET page.

There is no front controller framework and no middleware stack. Routing is file-based and intentionally obvious.

## Data path

Ideas are rows in SQLite. Each idea has a title, notes, status, and timestamps. The database file lives with the application data directory on the server. Backups are a hosting concern: copy the SQLite file, not an abstract schema dump alone.

## Presentation

Templates are plain PHP. They expect arrays of idea records and a few page-level variables. Empty states are first-class: an ideas list with zero rows is a normal condition, not an error.

## What is not in the path

- No session login gate
- No CSRF framework beyond careful form handling conventions
- No object storage or multipart upload handling
- No background workers

If you add any of those, update this page in the same change set.
