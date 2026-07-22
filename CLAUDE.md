# CLAUDE.md — Canonical operating rules for AI agents working on VibeKB

This is the **single, repository-owned** operating document for every coding
agent working on VibeKB (Claude Code, Cursor, Codex, and others). `AGENTS.md` and
`.cursor/rules/vibekb.mdc` are thin pointers to this file — do not duplicate the
workflow into them. Read this before doing meaningful work.

## Start every session with one command

```bash
php tools/vibekb.php status
```

It prints the active model's provenance, the current work record, the handoff's
next recommended action, and a one-line validation + drift summary. That is your
orientation — you do not need to read every file first.

## The product is locked

> **VibeKB exists so a vibe coder can open a software project at any point in its
> life and understand what the software is currently doing.**
>
> Primary promise: **Understand what your software is doing.**

**Software functionality is the primary subject of VibeKB. Repository memory
exists to keep the explanation of that functionality accurate, understandable,
and resistant to drift.** Do not turn VibeKB into a repository-memory product, a
documentation generator, a code browser, or an AI activity log. See
[PRODUCT.md](./PRODUCT.md).

## VibeKB is self-hosted

The active `.vibekb/` model in this repository describes **VibeKB itself** —
VibeKB explaining VibeKB. When you change VibeKB's code, you are changing the
software this model describes, so you must keep the model in step (that is the
whole point of self-hosting).

Bundled models of **other** applications live under `examples/` (e.g.
`examples/sousmeow/.vibekb/`, and `examples/field-tests/` for integration
audits). They are demonstration and fixtures — **never** the active model, and
never to be confused with the current state of VibeKB. You can preview or
validate one with `VIBEKB_CONTENT_ROOT=examples/sousmeow/.vibekb` or
`php tools/vibekb.php validate examples/sousmeow/.vibekb`. Do not modify example
content except to keep it valid.

## Where things live

- `PRODUCT.md` — canonical product definition. `SCHEMA.md` — the content model.
- `.vibekb/` — the **active** model (VibeKB's own), the source of truth,
  including `.vibekb/diagrams/` (source-grounded SVGs + explainable topology).
- `guide/` — the PHP V1 app (Mode A) and the shared `lib/` that also powers the
  static generator: templates, URL strategy per mode, provenance, nav, search.
- `tools/` — `vibekb.php` (the self-maintenance CLI), `generate-static.php`
  (Mode B → `/docs`), `validate.php` (headless validator), `test-topology.php`.
- `/docs` — **generated output** (the static snapshot). Never hand-edit it;
  change `.vibekb/` (or the templates) and regenerate.
- `examples/` — bundled example models and field-test material (not active).
- `MAINTENANCE.md` — the change lifecycle. `INITIALIZE.md` /
  `prompts/INTEGRATE_VIBEKB.md` — adding VibeKB to another repository.

## Two output modes, one source, honest provenance

The dynamic guide (Mode A) and the static `/docs` snapshot (Mode B) render the
same `.vibekb/` through the same templates. Every rendering carries objective
provenance (source commit analysed, analysis generated, verification scope) and
must never imply it auto-updates. VibeKB is **agent-maintained**: it detects that
code changed but never claims to interpret a change on its own.

## The maintenance lifecycle (follow it for every behaviour change)

The CLI makes each step low-friction; the steps are the same whether you change
VibeKB itself or a repository VibeKB is initialized in.

1. **Orient.** `php tools/vibekb.php status`. Read the current functionality,
   affected records, active warnings, and the handoff's next action.
2. **Record the work.** Before implementing, update `.vibekb/work/current.md`:
   requested outcome, current vs proposed behaviour, affected functionality,
   expected files, data impact, risks, verification plan.
3. **Implement** the code change.
4. **Find what it affects.** `php tools/vibekb.php affected --since <base>` (or
   pass files). Do not skip this — a changed file with no record is a signal, not
   noise.
5. **Verify.** Trace or exercise the real behaviour. Record what you tested and
   set the honest verification state — never claim `verified-*` for something you
   only inferred.
6. **Update the living model.** Bring the affected functionality records, system
   docs, files, diagrams (and their topology + SVG markers), memory, provenance,
   and status into line with the new behaviour. **Writing code is not "done."**
7. **Update the handoff.** `.vibekb/work/handoff.md`: current state, completed
   work, verification, unresolved work, active warnings, and the exact next
   action. Clear/refresh `current.md`.
8. **Check and refresh.** `php tools/vibekb.php check` (validate + broken
   references + drift + `/docs` sync), then `php tools/vibekb.php generate` to
   refresh `/docs`. Both must be clean before you commit.

## Truth and provenance rules

- Distinguish intended, implemented, and verified behaviour everywhere.
- Do not describe planned functionality as implemented, or claim it works because
  a file exists or an AI said so.
- Do not hide uncertainty — mark it (`unknown`, `needs-verification`,
  `not-verified`, `broken`, `inferred-from-source`).
- Keep `updates_automatically` false unless a real, verified update mechanism
  exists. Never fabricate source line numbers.

## Technical guardrails

Keep the app runnable on PHP 8.2 shared hosting, deployable in a subfolder,
usable without JavaScript, without a database, without an external/AI API, and
without a build step. Escape all output. Confine file access to the content root.
Do not introduce a framework, SPA, bundler, or SQL database.

## Deployment maintenance

When repository structure, runtime folders, or deployment requirements change,
update `.cpanel.yml` and `DEPLOYMENT.md` in the same change. The deployment
configuration is part of the application.

## Before you finish

`php tools/vibekb.php check` clean (no errors), `php tools/test-topology.php` OK,
`/docs` regenerated, and the handoff accurate. Then commit.
