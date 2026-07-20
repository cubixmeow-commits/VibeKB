---
id: verify-email
type: functionality
title: Verify your email
area: accounts-auth
summary: Confirms ownership of the email via a hashed, expiring token, unlocking the write actions gated by requireVerified.
status: implemented
verification: verified-from-source
user_facing: true
trigger: The user opens the "/verify-email/{token}" link (or resends it).
updated: 2026-07-16
tags: [auth, email, verification]
files: [app/Controllers/AuthController.php, app/Views/auth/verify-pending.php, app/Models/User.php, app/Core/Auth.php, app/Services/AccountMailer.php]
reads: [users]
writes: [users, rate_events]
config: [mail.driver, app.url]
depends_on: [register-account]
related_memory: [warning:password-reset-depends-on-smtp]
---

## In one sentence

Clicking the emailed link marks the account verified, which is what
`Auth::requireVerified()` checks before any write.

## Current behavior

`verifyEmail($token)` looks up the user by token (invalid/expired → message),
marks the email verified, refreshes the session, and routes to onboarding (for
non-simulation, not-yet-onboarded users) or the kitchen. `resendVerification()`
re-issues a token behind its own rate limits.

## Step-by-step flow

1. User opens `/verify-email/{token}`.
2. `User::findByVerificationToken()` resolves it, or an error redirect fires.
3. `User::markEmailVerified()` sets the timestamp.
4. The current session refreshes so `requireVerified()` now passes.
5. Redirect to onboarding or the kitchen.

## Implementation map

- `app/Controllers/AuthController.php` — `verifyEmail`, `resendVerification`,
  `showVerifyPending`.
- `app/Models/User.php` — token issue/verify.
- `app/Core/Auth.php` — `isVerified`, `requireVerified`, `refresh`.

## Data used

- **Reads/Writes:** `users` verification columns; `rate_events` for resends.

## Dependencies

Starts from **Register an account**; needs email delivery.

## Dependents

Every `requireVerified()` write: projects, pantry, runner actions, export.

## Failure cases

- Invalid/expired token → message + redirect.
- Mail fails on resend → error + `?delivery=failed`.

## Current state

- **Status:** implemented. **Verification:** verified from source
  (`AuthController` verification methods traced).

## Use caution

Because writes are gated on verification, an unconfigured mailer effectively
blocks new users from running Cookbooks — see the SMTP warning.

## Related functionality

- Register an account
- Complete onboarding (via Manage your account)
