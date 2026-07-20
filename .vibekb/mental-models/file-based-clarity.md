---
title: File-based PHP clarity over framework magic
summary: Prefer obvious PHP entry scripts and templates so a human can follow a change without learning a framework’s lifecycle.
updated: 2026-07-08
order: 3
---

## Model

The application stays understandable because the control flow is visible: this file handles this URL shape, that template renders this page.

## Implications

- Resist introducing a heavy framework for a single-user idea list.
- If a framework arrives later, rewrite this mental model and the project map in the same change.
