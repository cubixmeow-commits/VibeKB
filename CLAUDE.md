# CLAUDE.md — Operating rules for AI agents working on VibeKB

Read this before doing meaningful work in this repository. It applies to Claude
Code and any other coding agent.

## The product is locked

> **VibeKB exists so a vibe coder can open a software project at any point in
> its life and understand what the software is currently doing.**
>
> Primary promise: **Understand what your software is doing.**

**Software functionality is the primary subject of VibeKB. Repository memory
exists to keep the explanation of that functionality accurate, understandable,
and resistant to drift.**

**Do not turn VibeKB into a repository-memory product, a documentation
generator, a code browser, or an AI activity log.** Do not reinterpret the
product. If a change would make VibeKB primarily about anything other than
"what the software is doing right now," it is wrong.

See [PRODUCT.md](./PRODUCT.md) for the full definition.

## Where things live

- `PRODUCT.md` — canonical product definition.
- `SCHEMA.md` — record types, fields, statuses, verification, relationships,
  validation rules.
- `.vibekb/` — the repository-owned content (the source of truth for the model).
- `guide/` — the PHP V1 app that renders `.vibekb/` as the guide.
- `MAINTENANCE.md` — the workflow for changing a feature.
- `INITIALIZE.md` — the process for adding VibeKB to another repository.

## The required workflow for meaningful work

Follow this whenever you change behaviour. (For the example content model it is
about the documented sample app; for a real repo it is about that app.)

### 1. Understand current functionality first

Read: project identity (`.vibekb/project/`), the current overview, the affected
functionality records, the relevant files, active constraints, relevant
decisions, active warnings, and the current handoff (`.vibekb/work/handoff.md`).
**Be able to explain what the software currently does before changing it.**

### 2. Record current work

Before implementing, update `.vibekb/work/current.md` with: the requested
outcome, current behaviour, proposed behaviour, affected functionality,
expected files, data impact, risks, and a verification plan.

### 3. Implement

Make the code changes. Do not silently contradict product intent or the active
constraints. **Do not mark work complete merely because code was written.**

### 4. Verify

Test the real functionality where possible. Record what was tested, how, what
passed, what failed, and what remains unverified. Set the honest verification
state — never claim `verified-*` for something you only inferred.

### 5. Update the living software model

After a behaviour change, update the affected functionality records, flows,
data behaviour, file records, dependencies, failure cases, safety guidance,
status, and verification state. **The `.vibekb/` model must describe the
current software, not the previous version.**

### 6. Update repository memory

Capture only meaningful decisions, constraints, assumptions, warnings,
discoveries, and changes. **Do not save raw chat transcripts or every edit.**
Every memory record must link to the functionality (or files/data/work) it
affects.

### 7. Hand off

Update `.vibekb/work/handoff.md` with current functionality, completed work,
verification, unresolved work, active warnings, and the exact next recommended
action.

## Truth and provenance rules

- Distinguish intended, implemented, and verified behaviour everywhere.
- Do not describe planned functionality as implemented.
- Do not claim functionality works because a file exists or because an AI said
  so.
- Do not hide uncertainty — mark it (`unknown`, `needs-verification`,
  `not-verified`, `broken`).

## Technical guardrails

Keep the app runnable on PHP 8.2 shared hosting, deployable in a subfolder,
usable without JavaScript, without a database, without an external/AI API, and
without a build step. Escape all output. Confine file access to `.vibekb/`.
Do not introduce a framework, SPA, bundler, or SQL database.

## Deployment maintenance

When repository structure, runtime folders, or deployment requirements change,
update `.cpanel.yml` and `DEPLOYMENT.md` in the same task. The deployment
configuration is part of the application.
