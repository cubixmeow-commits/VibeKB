---
id: handoff
type: handoff
title: Current handoff
summary: SousMeow is now the canonical VibeKB example, source-grounded read-only. The full Run-a-Cookbook loop is verified; a few areas remain inferred and should be traced next.
updated: 2026-07-20
verification_state: mixed
---

## What the software (SousMeow) currently does

SousMeow packages proven workflows as Cookbooks of Recipes. A user stocks a
Pantry, runs each Recipe's prompt in their own AI, pastes the answer back,
confirms human Quality Checks, approves, and exports a Project Kit. SousMeow
never calls an AI. It runs as plain PHP 8 on Hostinger shared hosting with
SQLite (dev) or MySQL (production).

## Current functionality state

- **Verified from source (solid):** discovery (home, marketplace, categories,
  collections), auth (register, sign-in, verify), the full Runner (run-recipe,
  build-prompt, paste-response, review-quality-checks, approve-and-version),
  export-project-kit, admin overview, routing/security, database access.
- **Partial:** `reset-password` (web flow depends on SMTP the default deploy
  lacks); `demo-simulation` (paste-example verified; bulk simulation inferred).
- **Inferred from source:** `manage-account` (AccountController not line-traced),
  `seed-and-sync-content` (scripts/seed.php not line-traced), the `Router`,
  and some Model queries.

## Provenance note (important)

This model was derived by reading SousMeow read-only. SousMeow is **not** bundled
into VibeKB. It can go stale — **re-verify against the SousMeow source
(`cubixmeow-commits/dev-portfolio-v2`, `projects/sousmeow`) before changing any
functionality claim.**

## Changes completed this pass

- Replaced the entire SaaS Idea Manager sample with the SousMeow model.
- Homepage live-example + guide overview now feature SousMeow with counts
  computed from the loaded content.

## Verification completed

- Flagship "Run a Cookbook" source-traced end to end (files listed in the
  session record).
- VibeKB validation clean; `php -l` clean; all views load; 404s behave.

## Active warnings

- `read-write-path-coupling` — change Pantry/Artifact read and write paths together.
- `pasted-response-is-untrusted` — keep pasted content escaped, including in kit.html.
- `password-reset-depends-on-smtp` — web reset silently no-ops without SMTP.
- `legacy-category-column` — never read `cookbooks.category`.

## Assumptions requiring verification

- `ai-output-is-markdownish` — confirm output-contract parsing against real responses.

## Exact next recommended action

Trace `app/Controllers/AccountController.php`, `scripts/seed.php`, and the
`Simulation*` services in the SousMeow source, then promote `manage-account`,
`seed-and-sync-content`, and `demo-simulation` from inferred to verified (or
correct them). Re-run VibeKB validation afterward.
