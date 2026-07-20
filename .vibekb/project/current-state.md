---
id: project-current-state
type: project
title: Current application status
summary: The full create-to-export loop is implemented and source-verified. 31 executable + 2 preview Cookbooks are seeded. Password reset and demo simulation carry caveats.
status: implemented
verification: verified-from-source
updated: 2026-07-20
---

## Right now, the software can

- Show a marketing home, a searchable marketplace, categories, and collections
  over the Cookbook catalog.
- Register an account, verify email, sign in (rate-limited), and manage account
  settings, data export, and deletion.
- Start a project from an executable Cookbook, fill and validate a Pantry, and
  run the Recipe loop: build a prompt, paste a response as an immutable version,
  confirm per-version Quality Checks, and approve behind an all-checks gate.
- Preserve full artifact version history (paste / example / edited / restored)
  and export an approved project as a Project Kit zip (Markdown + offline HTML
  reader + manifest).
- Render an admin overview and a portfolio Demo Mode / simulation dashboard.

> Verified from source across `app/routes.php`, the controllers, services, and
> both schema dialects.

## Counts (from the seed source, not the README)

- **31 executable Cookbooks + 2 preview ("coming soon")** — counted from
  `database/seeds/cookbooks/*.php` (`is_executable => true` × 31, `false` × 2).
- The `README.md` still says "Twenty-two executable Cookbooks"; that number is
  stale. See the `cookbook-count-drift` discovery.

## Partial / uncertain areas

- **Password reset (web):** a full forgot/reset flow exists in
  `AuthController`, but it depends on email delivery. The default mail driver is
  `log` (writes `.eml` files, sends nothing), and the README frames resets as an
  admin CLI action. So in a default deploy the web reset appears to work but no
  email arrives. See `reset-password` (partial) and the `password-reset-depends-on-smtp` warning.
- **Demo Mode / simulation:** the Runner's "paste example response" path is
  source-verified; the 772-creator simulation scripts (`scripts/simulate-*.php`,
  `Services/Simulation*`) were read at the service boundary but not fully traced.
  See `demo-simulation` (verification: inferred-from-source).

## Active warnings

- Read and write paths for artifacts and pantry must move together when the
  schema changes (`read-write-path-coupling`).
- Pasted AI responses are untrusted input and must stay escaped end to end
  (`pasted-response-is-untrusted`).
- Password reset silently no-ops without SMTP configured
  (`password-reset-depends-on-smtp`).

## Last meaningful update

Source snapshot: commit `c1617ab`, 2026-07-16 (dev-portfolio-v2).
