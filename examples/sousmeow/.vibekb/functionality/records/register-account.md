---
id: register-account
type: functionality
title: Register an account
area: accounts-auth
summary: Creates a user, logs them in, and sends an email verification link (rate-limited, terms required).
status: implemented
verification: verified-from-source
user_facing: true
trigger: A visitor submits the registration form at "/register".
updated: 2026-07-16
tags: [auth, account, write]
files: [app/Controllers/AuthController.php, app/Views/auth/register.php, app/Models/User.php, app/Services/AccountMailer.php, app/Core/RateLimiter.php]
reads: [users, rate_events]
writes: [users, rate_events]
config: [mail.driver, app.url]
depends_on: []
related_memory: [warning:password-reset-depends-on-smtp]
---

## In one sentence

Name, email, and a password of at least 8 characters create an account, log the
user in, and trigger a verification email.

## Current behavior

`AuthController::register()` validates name, email, password length, match,
and terms acceptance; rate-limits new accounts per IP; rejects duplicate
emails; creates the user (`User::create`), logs in, issues a verification token,
and sends it via `AccountMailer`. On mail failure it still proceeds and shows a
resend path. Redirects to `/verify-email/pending`.

## Step-by-step flow

1. Visitor submits `/register`.
2. `AuthController::register()` validates all fields (422 + re-render on error).
3. Rate limit per IP is checked; duplicate email is rejected.
4. `User::create()` writes the user; `Auth::login()` starts the session.
5. A verification token is issued and emailed.
6. Redirect to `/verify-email/pending`.

## Implementation map

- `app/Controllers/AuthController.php` — `register()`, `showRegister()`.
- `app/Models/User.php` — `create`, `issueVerificationToken`.
- `app/Services/AccountMailer.php` — `sendVerification`.
- `app/Core/RateLimiter.php` — abuse control.

## Data used

- **Inputs:** name, email, password (+ confirm), terms.
- **Writes:** a `users` row; `rate_events`.

## Dependencies

Email delivery for the verification link (see **Verify your email**).

## Dependents

**Verify your email** and every write action gated by `Auth::requireVerified()`.

## Failure cases

- Invalid/duplicate input → 422 with messages, nothing written.
- Mail send fails → account created; a resend path is offered; error logged.

## Configuration

`mail.driver` (`log` writes `.eml` locally, `smtp` sends); `app.url` builds the
link.

## Current state

- **Status:** implemented. **Verification:** verified from source
  (`AuthController::register` traced).

## Use caution

Email delivery depends on SMTP config; with the default `log` driver no real
email is sent — see the `password-reset-depends-on-smtp` warning (same mailer).

## Related functionality

- Verify your email
- Sign in
