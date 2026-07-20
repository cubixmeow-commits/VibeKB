---
title: Session — Adding the status field
summary: Cursor session that added draft/active/archived status and documented why priorities stayed manual.
date: 2026-02-18
order: 1
---

## Prompt context

“Add a status field so ideas can be draft, active, or archived.”

## What the AI implemented

- Migration adding `status` column with default `draft`
- Form select on create and edit
- List filter by status

## What VibeKB captured (same session)

- Why three states, not a full workflow engine
- Rejected: automatic status transitions from date fields
- Warning: changing status options requires migration + all form templates

## Handoff for next session

Read `feature-timeline/status-field.md` before adding more states.
