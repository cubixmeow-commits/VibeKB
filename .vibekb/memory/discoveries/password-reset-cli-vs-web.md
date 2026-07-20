---
id: password-reset-cli-vs-web
type: discovery
title: Two password-reset paths exist, and they can disagree
summary: The code has a full web forgot/reset flow, while the docs present resets as an admin CLI action — reconciled by the fact that the web path needs SMTP the default deploy lacks.
status: resolved
verification: verified-from-source
updated: 2026-07-20
functionality: [reset-password]
files: [app/Controllers/AuthController.php, scripts/seed.php, docs/DEPLOYMENT.md, README.md]
tags: [auth, documentation, operations]
---

## Discovery

At first read the source and the docs seemed to contradict: `AuthController`
implements a complete web forgot/reset flow, but `README.md` and
`docs/DEPLOYMENT.md` say password resets are an admin CLI action.

## Evidence

- `AuthController::forgotPassword/resetPassword` are fully implemented and
  source-verified.
- The default `mail.driver` is `log` (writes `.eml`, sends nothing), and the
  README lists "no SMTP" among the deliberate v1 limits.
- `scripts/seed.php --reset-password` is documented as the operational path.

## Reconciliation

Both are true. The web flow is real and correct but only *useful* once SMTP is
configured; in a default deploy the reliable path is the CLI. This is why
`reset-password` is classified **partial**, not broken.

## Affected functionality

`reset-password`.

## Did it change the software model?

Yes — it set the honest status/verification for `reset-password` and produced
the `password-reset-depends-on-smtp` warning.
