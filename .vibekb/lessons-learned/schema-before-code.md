---
title: Read the schema before generating code
summary: AI will happily write forms and queries for columns that do not exist yet.
date: 2026-02-25
order: 1
---

## Lesson

When adding a field, the migration must land in the same change set as the form and write path.

## What broke once

Status field added to templates before migration ran on production.

## Now standard practice

1. Migration file
2. Update write path
3. Update read path
4. Update forms
5. Note in guide what breaks if skipped
