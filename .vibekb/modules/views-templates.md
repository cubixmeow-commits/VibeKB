---
title: Views and templates
summary: Server-rendered PHP templates for lists, forms, and detail pages with explicit empty states.
path: templates directory
updated: 2026-07-10
order: 3
---

## Responsibility

Render HTML from arrays of idea data without hiding control flow in a client framework.

## Contains

- Layout chrome
- Ideas list and detail
- Forms for create/edit
- Empty and error messaging

## Watchouts

Templates that assume keys always exist will break after partial migrations. Prefer explicit empty checks.
