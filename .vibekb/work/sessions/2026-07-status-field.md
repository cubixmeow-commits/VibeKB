---
id: 2026-07-status-field
type: session
title: Add the idea status lifecycle
summary: Added a status field and lifecycle to ideas, with a detail-page control to change it.
date: 2026-07-18
verification: verified-manually
functionality: [change-idea-status, create-idea, browse-ideas, view-idea, initialize-database]
files: [migrations/002_add_status.sql, src/IdeaService.php, src/IdeaRepository.php, public/update-status.php, templates/idea-detail.php, templates/ideas-list.php]
change: added-status-field
tags: [feature, status]
---

## Objective

Give each idea a status so the operator can see, at a glance, which ideas are
just captured versus actively being built.

## Prior software behavior

Ideas had title, notes, and priority only. No lifecycle.

## Requested behavior

A fixed status set with a control on the detail page and badges in the list.

## Work performed

- Added `002_add_status.sql` (new `status` column, default `inbox`).
- Added the allowed-status guard to `IdeaService`.
- Added `updateStatus()` to `IdeaRepository` and `public/update-status.php`.
- Showed the badge in `ideas-list.php` and `idea-detail.php`.

## Functionality affected

`change-idea-status` (new), plus create/browse/view display and the migration.

## Files affected

See front matter.

## Data impact

Forward-only migration adding `status`, defaulting existing rows to `inbox`.

## Tests performed

Manual: created ideas and transitioned each through every status; confirmed
badges. No automated test added.

## Result

Shipped as the `added-status-field` change.

## Unresolved issues

No automated coverage; no status history log (only the latest status is kept).

## Memory records added or updated

Added the `added-status-field` change; reinforced the `read-write-path-drift`
warning; linked to the earlier `blank-list-ordering` discovery.
