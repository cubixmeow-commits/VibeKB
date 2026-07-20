---
id: handoff
type: handoff
title: Current handoff
summary: Core idea capture, browse, view, and status change are solid. Export needs verification. Do not add auth or auto-migrations.
updated: 2026-07-20
verification_state: mixed
---

## What the software currently does

Captures ideas, lists them by priority, opens a full detail view, moves ideas
through a fixed status lifecycle, and exports everything as CSV. Single
operator, SQLite, plain PHP on cPanel.

## Current functionality state

- **Solid (implemented, verified):** create-idea, view-idea,
  change-idea-status, initialize-database.
- **Partial:** browse-ideas (list and ordering work; no UI to reorder
  priority).
- **Experimental / unverified:** export-ideas (CSV escaping not yet tested).

## Current work

Verifying CSV export escaping — see the current-work record. In progress, not
yet verified.

## Changes completed

The status lifecycle (`added-status-field`) shipped on 2026-07-18 and was
verified manually.

## Verification completed

- create/view/status flows: verified manually.
- Schema migrations: verified from source.
- Export escaping: **not** verified — this is the open item.

## Incomplete work

- Priority reordering UI (browse-ideas is partial).
- Automated tests generally (only manual verification exists).

## Active warnings

- `read-write-path-drift` — change read and write paths together.
- `half-auth-not-multiuser` — do not add login without ownership.
- `no-silent-migrations` — never auto-migrate on a page load.

## Assumptions requiring verification

- `low-volume-single-writer` — untested above a few hundred ideas.

## Exact next recommended action

Add a test that exports a note containing a comma, a double quote, and a
newline; fix `public/export.php` if it fails; then update the `export-ideas`
record to reflect the real status.
