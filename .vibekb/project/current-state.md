---
id: project-current-state
type: project
title: Current application status
summary: Core capture, browse, view, status change, and CSV export are implemented. Priority sort is partial. Export is experimental.
status: implemented
updated: 2026-07-18
---

## Right now, the software can

- Capture a new idea and store it durably in SQLite.
- Show all ideas in one list, most important first.
- Open a single idea to read its full notes and history of status.
- Move an idea through its status lifecycle (`inbox → exploring → building → shipped → parked`).
- Export the whole list as a CSV file.

## Not finished / uncertain

- **Priority ordering** is partially implemented: ideas sort by a numeric
  `priority` column, but there is no UI to reorder — priority is set only at
  creation. See `browse-ideas` (status: partial).
- **Export** is experimental: it works for the current columns but has not been
  verified against ideas containing commas or newlines in notes. See
  `export-ideas`.

## Active warnings

- Read and write paths must be changed together when fields change
  (`read-write-path-drift`).
- A login page alone would not make this safely multi-user
  (`half-auth-not-multiuser`).

## Last meaningful update

2026-07-18 — added the `status` field and lifecycle. See the change record
`added-status-field` and session `2026-07-status-field`.
