---
id: cli-only-admin
type: constraint
title: Admin and password rotation are CLI-only
summary: Admin accounts exist only via scripts/seed.php, which prints a one-time password; there is no web installer and the admin surface is read-only.
status: active
verification: verified-from-source
updated: 2026-07-16
functionality: [view-admin-overview, seed-and-sync-content, reset-password]
files: [scripts/seed.php, app/Controllers/AdminController.php, docs/DEPLOYMENT.md]
tags: [security, admin, operations]
---

## Constraint

There is no web-based installer or admin creation. Admin accounts are
provisioned by `php scripts/seed.php` (CLI-only; 404 over HTTP), which prints a
temporary password once. The web `/admin` surface is read-only. Password
rotation is available via `scripts/seed.php --reset-password`.

## Source

`docs/DEPLOYMENT.md`, `AdminController.php` (read-only).

## Affected functionality

Admin overview, seeding, and (operationally) password reset.

## Consequences

- No privileged web surface to attack for account takeover.
- Operators need shell access to provision admin or rotate passwords — which is
  also why the web password-reset flow is a secondary path (see the
  `password-reset-depends-on-smtp` warning).

## Still active?

Yes.
