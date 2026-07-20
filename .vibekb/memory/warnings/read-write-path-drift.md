---
id: read-write-path-drift
type: warning
title: Read and write paths must change together
summary: When you add or rename an idea field, update the migration, the create/update writes, and every read path in the same change — or ideas silently lose data.
severity: high
status: active
verification: verified-from-source
created: 2026-03-15
updated: 2026-07-16
functionality: [create-idea, browse-ideas, view-idea, change-idea-status]
files: [src/IdeaRepository.php, src/IdeaService.php]
tags: [data-integrity, gotcha]
---

## Affected functionality

Every idea read or write.

## What can go wrong

A new field is added to the create form and the `INSERT`, but a read query
still selects the old column set. The field appears to "disappear" — it was
written but never read back. The reverse (reading a column that create never
writes) yields nulls or errors.

## Cause

Field logic is spread across the migration, `IdeaService`, `IdeaRepository`
insert/update, the read queries, and the templates. Changing one without the
others breaks alignment.

## What not to do

Do not add a column to only the form and insert. Do not "temporarily" read a
field that create does not yet write.

## Safe procedure

1. Write the migration (new column with a default).
2. Update `IdeaService` validation/normalisation.
3. Update the `INSERT`/`UPDATE` in `IdeaRepository`.
4. Update every `SELECT` and the templates that display it.
5. Apply the migration and exercise create → list → view.

## Verification steps

Create an idea, confirm the new field survives a round-trip through the list
and detail pages.
