---
title: Blank ideas list
summary: An empty list is usually a normal empty database, a failed query, or a template that hides rows incorrectly—not a missing route.
category: ui
updated: 2026-07-14
order: 1
---

## Check in order

1. Confirm the SQLite file exists and is readable by the PHP user.
2. Run or inspect a simple `SELECT COUNT(*)` against the ideas table.
3. Verify the list template’s empty-state branch is not always true.
4. Confirm the query selects the columns the template prints.

## Common cause

A migration added columns locally; production still has the old table; the list query throws and the page fails open into a blank state depending on error display settings.
