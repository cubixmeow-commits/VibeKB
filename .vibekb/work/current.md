---
id: current-work
type: work
title: Model SousMeow as the canonical VibeKB example
summary: Replace the fictional SaaS Idea Manager sample with a truthful, source-derived VibeKB model of the real SousMeow application.
objective: Turn SousMeow into the example shown throughout VibeKB, grounded entirely in its source.
requested_by: Project owner
status: completed
verification_state: verified-from-source
updated: 2026-07-20
affected_functionality: [run-recipe, export-project-kit, browse-marketplace, review-quality-checks]
expected_files: [.vibekb/project, .vibekb/functionality, .vibekb/system, .vibekb/files, .vibekb/memory, .vibekb/work, index.php]
data_impact: None to SousMeow (read-only). VibeKB content only.
risks: [Stale or invented documentation if SousMeow changes and the model is not re-verified.]
---

## What the user asked for

Replace the fictional "SaaS Idea Manager" sample with a real VibeKB example
based on **SousMeow** (`cubixmeow-commits/dev-portfolio-v2`, under
`projects/sousmeow`), keeping VibeKB the product and SousMeow the real
application it explains. SousMeow must not be redesigned, modified, or bundled.

## What the old sample contained

A fictional single-user PHP + SQLite "SaaS Idea Manager" with six invented
functionality records (create-idea, browse-ideas, etc.) whose source files did
not exist in any repository.

## What the guide contains afterward

A source-grounded model of SousMeow: 25 functionality records across 9 capability
groups, six system documents, ~31 curated important files, source-grounded
decisions/constraints/assumptions/warnings/discoveries/changes, and this
handoff. Every claim traces to SousMeow source; verification states are honest.

## How SousMeow source was used

Read-only. The repository was cloned to a scratch directory and inspected; no
change was made to SousMeow. File paths in records are relative to
`projects/sousmeow/`.

## Risks

The largest risk is drift: if SousMeow changes, these records can go stale. The
handoff and manifest instruct future agents to re-verify against source before
changing functionality claims.

## Completed work

- Audited SousMeow: routes, schema (both dialects), front controller, database,
  the full Runner chain, discovery, auth, admin, export, and stats/simulation.
- Removed all SaaS Idea Manager content and rebuilt `.vibekb/` around SousMeow.
- Updated the homepage live-example section and guide labels to SousMeow.

## Remaining / recommended next task

Trace the areas currently marked `inferred-from-source` directly — especially
`AccountController` (manage-account), `scripts/seed.php` (seed-and-sync-content),
and the `Simulation*` services (demo-simulation) — and promote their verification
states once confirmed.

## How the result was verified

VibeKB validation (Reference view) shows no unresolved errors; `php -l` passes;
all guide views and every functionality detail load; flagship "Run a Cookbook"
was source-traced through the exact files listed in the handoff.

## Repository memory added

Decisions, constraints, assumptions, warnings, discoveries, and one change
record — all linked to SousMeow functionality.
