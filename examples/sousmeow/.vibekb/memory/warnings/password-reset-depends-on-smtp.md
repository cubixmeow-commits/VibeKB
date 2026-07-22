---
id: password-reset-depends-on-smtp
type: warning
title: Web password reset silently no-ops without SMTP
summary: The forgot/reset flow is coded correctly but only useful when a real mail driver is configured; the default 'log' driver sends nothing, so the CLI is the reliable path.
severity: medium
status: active
verification: verified-from-source
updated: 2026-07-16
functionality: [reset-password, register-account, verify-email]
files: [app/Controllers/AuthController.php, app/Services/AccountMailer.php, config/config.example.php, .env.example]
tags: [email, operations, gotcha]
---

## Affected functionality

Password reset, and by the same mailer, registration verification emails.

## What can go wrong

`forgotPassword()` always shows the neutral "check your email" screen (correct,
to avoid enumeration), but with the default `mail.driver = log` no email is
actually sent — it is written to `storage/mail/` as an `.eml` file. A user
waiting on the email is stuck, and verification emails have the same dependency.

## Cause

Email delivery is optional in v1; the README frames resets as an admin CLI
action (`php scripts/seed.php --reset-password`).

## What not to do

Do not assume the web reset/verification works end to end without confirming
SMTP is configured.

## Safe procedure

- Configure `.env` SMTP and set `MAIL_DRIVER=smtp` for real delivery, or
- Use `php scripts/seed.php --reset-password you@example.com` for resets.
- In dev, read the `.eml` files under `storage/mail/`.

## Verification steps

With `MAIL_DRIVER=log`, confirm the `.eml` file appears and no real email is
sent; with SMTP set, confirm delivery.
