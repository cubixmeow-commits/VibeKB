---
title: Schema drift across environments
summary: Manual migrations mean local, staging, and production SQLite files can disagree. Missing columns surface as blank pages or write failures.
severity: medium
status: active
updated: 2026-07-15
order: 3
---

## How it happens

A migration is applied locally and forgotten on the server. Or production is updated and the local file is recreated from an old schema.

## Symptoms

- SQL errors about missing columns
- Forms that save without error but omit new fields
- Templates that assume keys the query never selects

## Mitigation

Keep a short checklist: which migration files exist, which have been applied where, and what the current idea row shape should be. After deploy, create and edit one idea end to end.
