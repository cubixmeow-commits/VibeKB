---
title: Save path alignment checks
summary: Five places must agree or saves silently fail or display wrong data.
date: 2026-02-10
order: 2
---

## The five must agree

- Form field names
- Validation rules
- INSERT/UPDATE statement
- SELECT projection
- Template display

## When this saved us

Blank list was a read-path issue. Fields disappearing was a write/read mismatch.

## In the guide

Chapter on change safety uses this as the core mental model.
