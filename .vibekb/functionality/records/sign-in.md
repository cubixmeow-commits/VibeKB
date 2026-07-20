---
id: sign-in
type: functionality
title: Sign in
area: accounts-auth
summary: Authenticates a user against a hashed password with per-IP and per-email rate limiting and opportunistic hash upgrades.
status: implemented
verification: verified-from-source
user_facing: true
trigger: A visitor submits the login form at "/login".
updated: 2026-07-16
tags: [auth, login, write]
files: [app/Controllers/AuthController.php, app/Views/auth/login.php, app/Models/User.php, app/Core/Auth.php, app/Core/RateLimiter.php]
reads: [users, rate_events]
writes: [users, rate_events]
config: [session.cookie_name, session.idle_ttl, session.secure]
depends_on: []
related_memory: []
---

## In one sentence

Email plus password signs you in, throttled to defeat guessing, with the
session rotated on login.

## Current behavior

`AuthController::login()` validates the fields, checks two rate-limit windows
(20/15min per IP, 8/15min per email), verifies the password with
`password_verify`, logs in via `Auth::login()`, and rehashes the password if
`password_needs_rehash` says so. Errors return a generic "email and password do
not match" (no user enumeration).

## Step-by-step flow

1. Visitor submits `/login`.
2. Fields validated; rate-limit windows checked (both IP and email).
3. `User::findByEmail()` + `password_verify()`.
4. On success `Auth::login()` rotates the session and stores the user.
5. Password is transparently rehashed if the algorithm advanced.
6. Redirect to the intended page or `/kitchen`.

## Implementation map

- `app/Controllers/AuthController.php` — `login()`.
- `app/Core/Auth.php` — session, `login()`, `redirectIntended()`.
- `app/Core/RateLimiter.php` — `tooMany`, `hit`.

## Data used

- **Inputs:** email, password.
- **Writes:** `rate_events`; possibly an updated `password_hash`.

## Failure cases

- Wrong credentials → 422, generic message (no enumeration).
- Too many attempts → throttled message.

## Configuration

Session cookie name, idle TTL, and `secure` flag (true over HTTPS).

## Current state

- **Status:** implemented. **Verification:** verified from source
  (`AuthController::login` traced).

## Use caution

Keep the generic error message; a specific "no such user" reintroduces
enumeration.

## Related functionality

- Register an account
- Reset a password
