---
id: 2026-07-sousmeow-migration
type: session
title: Migrate the VibeKB example to SousMeow
summary: Replaced the fictional SaaS Idea Manager sample with a source-grounded VibeKB model of the real SousMeow application.
date: 2026-07-20
verification: verified-from-source
functionality: [run-recipe, build-prompt, paste-response, review-quality-checks, approve-and-version, export-project-kit]
files: [.vibekb/functionality, .vibekb/system, .vibekb/files, .vibekb/memory, index.php]
change: taxonomy-categories-collections
tags: [migration, example, sousmeow]
---

## Objective

Make SousMeow the canonical VibeKB example, grounded entirely in its source.

## Prior state

The example was a fictional SaaS Idea Manager whose source files existed in no
repository.

## Work performed

- Cloned `cubixmeow-commits/dev-portfolio-v2` read-only and located SousMeow at
  `projects/sousmeow`.
- Audited it and rebuilt the entire `.vibekb/` model around it.

## Source files examined (flagship: Run a Cookbook)

Traced end to end for the flagship workflow:

- `app/routes.php` — the full route map.
- `public/index.php` — front controller (CSP, CSRF gate).
- `app/Core/Database.php` — the single PDO path.
- `app/Controllers/ProjectController.php` — start project, Pantry save/validate.
- `app/Controllers/RunnerController.php` — the gather/review/approved loop and all
  write transitions (paste, example, checks, approve, reopen, edit, restore).
- `app/Services/PromptBuilder.php` — prompt assembly + artifact chaining.
- `app/Controllers/ExportController.php` + `app/Services/ProjectKit.php` — the
  export gate, zip assembly, and owner-scoped download.
- `database/schema.sqlite.sql` — the full data model.

Also read for other records: `README.md`, `docs/ARCHITECTURE.md`,
`docs/DEPLOYMENT.md`, `.env.example`, `config/config.example.php`,
`MarketingController`, `MarketplaceController`, `CategoryController`,
`CollectionController`, `KitchenController`, `AdminController`, `AuthController`,
`Services/SiteStats.php`, and the cookbook seed files (for counts).

## Result

25 functionality records, 6 system docs, ~31 important files, 5 decisions,
5 constraints, 2 assumptions, 4 warnings, 3 discoveries, 1 change; homepage and
guide updated. Validation clean.

## Unresolved issues

`AccountController`, `scripts/seed.php`, and `Simulation*` remain
inferred-from-source and should be traced next.

## Memory records added or updated

All SousMeow decisions/constraints/assumptions/warnings/discoveries and the
`taxonomy-categories-collections` change record.
