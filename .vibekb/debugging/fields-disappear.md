---
title: Write succeeds but fields disappear
summary: When new fields save in the form but vanish on reload, the write path and read path are out of sync.
category: data
updated: 2026-07-14
order: 3
---

## Check in order

1. Confirm the INSERT/UPDATE includes the new columns.
2. Confirm the SELECT used by detail/list includes those columns.
3. Confirm the template uses the same key names as the query aliases.
4. Confirm the column exists in SQLite at all.

## Pattern

Agents often update the form and forget either the write statement or the read statement. Fix both in one pass.
