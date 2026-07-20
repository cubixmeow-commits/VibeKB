# VibeKB

# Understand what your software is doing.

VibeKB gives AI-assisted developers a living explanation of their application's
current functionality — how it works, what AI is changing, and why. It lives in
your repository (`.vibekb/`) and renders as a website (`guide/`).

VibeKB exists so a vibe coder can open a software project at any point in its
life and understand **what the software is currently doing** — not just what
someone intended, and not just a pile of docs. See [PRODUCT.md](./PRODUCT.md).

## Who it's for

People who build with coding agents (Claude Code, Cursor, Codex, Windsurf,
Copilot, Gemini CLI). AI can change six files faster than you can rebuild your
mental model. VibeKB keeps that mental model accurate — organized around
**functionality**, the things your software actually does.

## How V1 works

- **Content** lives in `.vibekb/` as Markdown + small JSON manifests — readable
  by humans, editable by AI, versioned with your code. No database.
- **The guide** (`guide/`) is a plain PHP 8.2 app that loads that content,
  resolves the relationships between records, validates it, and renders it.
- **Functionality is the primary unit.** Each functionality record explains a
  behaviour in plain language, with a step-by-step flow, the files and data
  involved, dependencies, failure cases, and its real status and verification
  state.

### The V1 views

Overview · Functionality Index · Functionality Detail · How It Works ·
Data & Storage · Files That Matter · Current AI Work · Changes · Why It Works
This Way · AI Handoff · Reference.

The included `.vibekb/` content models a small example application (a SaaS Idea
Manager) so every view is demonstrated with realistic, connected content.

## Run locally

```bash
VIBEKB_DEV=1 php -S localhost:8080 -t .
```

Then open:

- http://localhost:8080/ — the homepage
- http://localhost:8080/guide/ — the Software Guide (V1)
- http://localhost:8080/guide/?view=reference — content model + validation

`VIBEKB_DEV=1` shows full errors and a validation banner; leave it unset for
production-style restraint.

## Deploy to cPanel

Plain PHP, no build step. The repository syncs into a cPanel public folder (or
a subfolder) via `.cpanel.yml`. The guide uses query-string routing, so **no
rewrite rules are required** and it works in a subfolder. `.vibekb/` must be
deployed (it is the content). See [DEPLOYMENT.md](./DEPLOYMENT.md).

## The `.vibekb/` structure

```
.vibekb/
  manifest.json
  project/        identity, intent, current-state, constraints
  functionality/  index.json + records/ (the primary unit)
  system/         mental-model, components, request-flow, data-flow, storage, deployment
  files/          important-files.json
  memory/         decisions, constraints, assumptions, warnings, discoveries, changes
  work/           current, handoff, sessions/
```

See [SCHEMA.md](./SCHEMA.md) for record types, fields, statuses, verification
states, and validation rules.

## How functionality records work

A functionality record is a Markdown file with front matter (id, status,
verification, area, trigger, files, reads/writes, dependencies, related memory)
and a narrative body (what it does, step-by-step flow, failure cases, what's
safe to change, why). The guide renders the narrative and turns the front-matter
relationships into live, validated links between functionality, files, data, and
memory.

## How current AI work is recorded

`.vibekb/work/current.md` holds the active objective: what was asked, what the
software does now, what it should do after, affected functionality, expected
files, data impact, risks, and progress. It renders as the **Current AI Work**
view so you can see what AI is doing before, during, and after a change.

## How repository memory supports functionality

Decisions, constraints, assumptions, warnings, discoveries, and changes each
link back to the functionality they explain. They keep the explanation accurate
as the software changes — they are not an isolated archive.

## Current V1 limitations

- The example content models a documented sample app; its source files are
  described, not shipped in this repo.
- Extraction is not automatic — records are written and maintained by you and
  your coding agent following the workflow in [MAINTENANCE.md](./MAINTENANCE.md).
- The Markdown renderer supports a pragmatic subset (headings, lists, tables,
  code, emphasis, links, blockquotes) — not full CommonMark.

## For AI agents

Read [CLAUDE.md](./CLAUDE.md) / [AGENTS.md](./AGENTS.md) before making changes,
[MAINTENANCE.md](./MAINTENANCE.md) for the change workflow, and
[INITIALIZE.md](./INITIALIZE.md) to add VibeKB to another repository.
