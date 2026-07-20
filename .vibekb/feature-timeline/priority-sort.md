---
title: Priority sorting — manual order only
summary: Month 2 addition; drag-free ordering for a list that should stay boring.
date: 2026-03-12
order: 3
---

## Why manual

Automatic priority from AI scores would imply confidence the project does not have.

## Implementation note

Integer `sort_order` column; renumber on save. Documented so agents do not add a full ranking engine.

## Dangerous to modify

Changing sort logic without updating list query breaks the home screen order users rely on.
