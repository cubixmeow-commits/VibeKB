---
id: no-checkout-v1
type: constraint
title: No payment or checkout in v1
summary: Marketplace previews show honest "coming soon" states with no checkout; there is no payment SDK, and only executable Cookbooks can start.
status: active
verification: verified-from-source
updated: 2026-07-16
functionality: [start-project, view-cookbook-detail, browse-marketplace]
files: [app/Controllers/ProjectController.php, README.md]
tags: [scope, commerce]
---

## Constraint

v1 has no checkout and no payment SDK. Two Cookbooks are marketplace previews
with `status = coming_soon` and `is_executable = 0`. Only executable Cookbooks
can start a project, enforced server-side.

## Source

`README.md` (deliberate limits), `ProjectController::create()` (executable gate).

## Affected functionality

Starting a project (preview Cookbooks are refused), and the marketplace/detail
"coming soon" states.

## Consequences

- `price_cents` exists on `cookbooks` but no purchase path consumes it.
- The executable gate is the real boundary; do not move it to the view.

## Still active?

Yes in v1.
