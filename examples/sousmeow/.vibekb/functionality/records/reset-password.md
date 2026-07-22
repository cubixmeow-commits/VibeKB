---
id: reset-password
type: functionality
title: Reset a password
area: accounts-auth
summary: A web forgot/reset flow exists and is coded correctly, but it depends on email delivery that the default deploy does not configure — so in practice resets happen via the CLI.
status: partial
verification: verified-from-source
user_facing: true
trigger: A visitor uses "/forgot-password", then the emailed "/reset-password/{token}" link.
updated: 2026-07-16
tags: [auth, password, email]
files: [app/Controllers/AuthController.php, app/Views/auth/forgot.php, app/Views/auth/reset.php, app/Models/PasswordReset.php, app/Services/AccountMailer.php, scripts/seed.php]
reads: [users, password_reset_tokens, rate_events]
writes: [users, password_reset_tokens, rate_events]
config: [mail.driver, app.url, app.base_path]
depends_on: []
related_memory: [warning:password-reset-depends-on-smtp, discovery:password-reset-cli-vs-web]
---

## In one sentence

Request a reset link and set a new password — but only if email delivery is
configured; otherwise the documented path is the admin CLI.

## Current behavior

`forgotPassword()` validates and rate-limits, then issues a token and emails it
**only** for real (non-simulation, non-admin) users, always showing the same
"sent" screen to avoid enumeration. `resetPassword($token)` validates the token
and the new password, consumes the token, updates the hash, invalidates other
tokens, and logs the user out. The default `mail.driver` is `log`, so no email
actually leaves the server unless SMTP is set. `docs/DEPLOYMENT.md` documents
`php scripts/seed.php --reset-password you@example.com` as the operational path.

## Step-by-step flow

1. Visitor submits `/forgot-password`.
2. Validated + rate-limited; a token is issued and (if SMTP) emailed.
3. The same neutral "check your email" screen always renders.
4. Visitor opens `/reset-password/{token}`; validity is checked.
5. New password validated, token consumed, hash updated, session logged out.

## Implementation map

- `app/Controllers/AuthController.php` — forgot/reset methods.
- `app/Models/PasswordReset.php` — `issue`, `findValid`, `consume`.
- `app/Services/AccountMailer.php` — `sendPasswordReset`.
- `scripts/seed.php` — the CLI `--reset-password` fallback.

## Data used

- **Writes:** `password_reset_tokens`, `users.password_hash`, `rate_events`.

## Current state

- **Status:** partial — the code path is complete and source-verified, but its
  usefulness depends on SMTP, which the default deploy does not enable.
- **Verification:** verified from source (both controller methods traced).

## Use caution

Do not assume the web reset works end to end without confirming SMTP. See the
`password-reset-depends-on-smtp` warning and the `password-reset-cli-vs-web`
discovery.

## Related functionality

- Sign in
- Manage your account
