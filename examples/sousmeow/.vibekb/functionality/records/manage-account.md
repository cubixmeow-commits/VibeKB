---
id: manage-account
type: functionality
title: Manage your account
area: accounts-auth
summary: Account settings — profile, preferences, security (password and email change), a personal data export, first-run onboarding, and account deletion.
status: implemented
verification: inferred-from-source
user_facing: true
trigger: The user opens "/account" (and its sub-pages) while signed in.
updated: 2026-07-16
tags: [account, settings, privacy]
files: [app/Controllers/AccountController.php, app/Views/account/index.php, app/Views/account/profile.php, app/Views/account/preferences.php, app/Views/account/security.php, app/Views/account/data.php, app/Views/account/onboarding.php, app/Services/AccountDataExport.php]
reads: [users, projects, artifacts, artifact_versions, exports]
writes: [users]
config: [app.url]
depends_on: [sign-in]
related_memory: []
---

## In one sentence

One place to edit profile and preferences, change password or email, export
your data, complete onboarding, and delete the account.

## Current behavior

The routes (`app/routes.php`) map `AccountController` to: `index`, `profile`
(+update), `preferences` (+update), `security` (+`updatePassword`,
`requestEmailChange`, `confirmEmailChange/{token}`), `data` (+`exportData`),
`deleteAccount`, and onboarding (`showOnboarding`, `completeOnboarding`). The
data export is built by `Services/AccountDataExport`. Email change is
confirmed via a token, mirroring email verification.

## Step-by-step flow

1. Signed-in user opens `/account`.
2. Sub-pages GET a form; POSTs update the `users` row (or issue a token).
3. Email change is confirmed via `/account/email/confirm/{token}`.
4. Data export assembles the user's records for download.
5. Account deletion cascades the user's data (schema `ON DELETE CASCADE`).

## Implementation map

- `app/Controllers/AccountController.php` — all account actions.
- `app/Services/AccountDataExport.php` — the personal-data export.
- `app/Views/account/*` — the settings pages.

## Data used

- **Reads/Writes:** primarily the `users` row; the data export reads the user's
  projects, artifacts, and exports.

## Current state

- **Status:** implemented. **Verification:** inferred from source — the routes,
  views, and services confirm the surface, but `AccountController` was not
  traced line by line in this pass. A future pass should verify the profile,
  security, and delete flows directly.

## Use caution

Account deletion relies on `ON DELETE CASCADE` across `projects`, `artifacts`,
and related tables; verify the cascade before changing those foreign keys.

## Related functionality

- Sign in
- Verify your email
