---
id: added-status-field
type: change
title: Added an idea status lifecycle
summary: Ideas gained a status field with a fixed lifecycle (inbox, exploring, building, shipped, parked) and a way to change it.
status: shipped
verification: verified-manually
created: 2026-07-18
updated: 2026-07-18
functionality: [change-idea-status, create-idea, browse-ideas, view-idea, initialize-database]
files: [migrations/002_add_status.sql, src/IdeaService.php, src/IdeaRepository.php, public/update-status.php, templates/idea-detail.php, templates/ideas-list.php]
session: 2026-07-status-field
tags: [feature, status]
---

## Before

An idea had only a title, notes, and priority. There was no way to record where
an idea sat in its lifecycle; the operator tracked that in their head.

## After

Every idea has a `status` from a fixed set (`inbox`, `exploring`, `building`,
`shipped`, `parked`). New ideas default to `inbox`. The status shows as a badge
in the list and detail, and can be changed from the detail page.

## What caused the change

The operator wanted to tell at a glance which ideas were merely captured versus
actively being built.

## Functionality affected

`create-idea` (defaults status), `browse-ideas` and `view-idea` (display it),
`change-idea-status` (new), `initialize-database` (new migration).

## Data impact

New `status` column via `002_add_status.sql`, defaulting existing rows to
`inbox`. Forward-only.

## Compatibility concerns

Any database that had not applied the migration would break the list — this is
exactly the `blank-list-ordering` discovery. Deploy code, then migrate.

## Verification performed

Manually created ideas and transitioned each through all statuses; confirmed
badges in list and detail. No automated test yet.

## Related decisions

Uses the existing `manual-migrations` decision for the schema change.
