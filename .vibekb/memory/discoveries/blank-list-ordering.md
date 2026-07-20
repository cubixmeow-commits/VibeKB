---
id: blank-list-ordering
type: discovery
title: The blank list was an ordering bug, not empty data
summary: Ideas existed but the list rendered empty because a read query referenced a column that a migration had not yet added on that database.
status: resolved
verification: verified-manually
created: 2026-05-11
updated: 2026-05-11
functionality: [browse-ideas, change-idea-status]
files: [src/IdeaRepository.php]
tags: [debugging, data-integrity]
changed_model: true
---

## Discovery

The ideas list rendered completely empty on a machine that definitely had
ideas in the database.

## Evidence

Running the list query by hand raised a SQLite error about an unknown column:
the read path referenced `status` before the `002_add_status.sql` migration had
been applied on that database. The controller swallowed the exception and
showed the empty state.

## Affected functionality

`browse-ideas` (rendered empty) and, upstream, `change-idea-status` (the field
that triggered it).

## Consequence

An unapplied migration can masquerade as "no data". The empty state is not
proof the database is empty.

## Action taken

- Applied the pending migration.
- Made the list controller distinguish a query error from a genuinely empty
  result, so a schema mismatch surfaces instead of looking like no ideas.

## Did it change the software model?

Yes — it reinforced the `read-write-path-drift` warning and the rule that the
empty state must never hide an error.
