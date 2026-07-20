---
id: system-deployment
type: system
title: Deployment
summary: Plain PHP synced to a cPanel subfolder; the SQLite file lives on the server and is never overwritten by a deploy.
updated: 2026-07-16
---

## How it ships

The app is plain PHP with no build step. It is deployed by syncing the
repository into a cPanel public folder (optionally a subfolder such as
`/ideas/`). Because there is no compilation, what is in the repo is what runs.

## What must not be overwritten

The live SQLite database sits on the server (for example under `data/`) and is
**excluded** from the deploy sync so a `--delete` rsync can never remove it.
This is the single most important deployment rule.

## Configuration on the server

`IDEAS_DB_PATH` is set in the server environment to point at the persistent
database location, outside the deploy tree if possible.

## Subfolder-safe

All in-app links are relative and derived from the request path, so the app
works whether it is served from the web root or a subfolder. No rewrite rules
are required.

## The constraint behind all of this

Everything here follows from the `php82-cpanel-subfolder` constraint: PHP 8.2,
shared hosting, subfolder deploy, no Node, no build.
