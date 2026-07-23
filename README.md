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

## Add VibeKB to your repository

VibeKB installs with one command. It prepares the workspace; your coding agent
builds the model.

```bash
git clone https://github.com/cubixmeow-commits/VibeKB.git
php VibeKB/install.php /path/to/your/project
```

The installer copies the VibeKB runtime (`guide/`, `tools/`, `prompts/`,
`.cursor/`, and the VibeKB docs) into your repository, scaffolds a fresh,
empty-but-valid `.vibekb/` workspace, and verifies the result — **without
touching your application's code and without analysing it.** Then open your
project in a coding agent (Claude Code, Cursor, Codex, …) and ask it to *build the
first VibeKB model using `prompts/INTEGRATE_VIBEKB.md`*.

- Preview the plan first: `php VibeKB/install.php --dry-run /path/to/your/project`
- Upgrade later (refresh runtime, keep your model): re-run the installer.
- Repair a workspace any time: `php tools/vibekb.php bootstrap`.

PHP 8.2+, no Composer, no network, cross-platform. See [INSTALLER.md](./INSTALLER.md)
for the full flow, upgrades, repairs, and the template structure.

## How V1 works

- **Content** lives in `.vibekb/` as Markdown + small JSON manifests — readable
  by humans, editable by AI, versioned with your code. This is the **single
  source of truth**. No database.
- **Functionality is the primary unit.** Each functionality record explains a
  behaviour in plain language, with a step-by-step flow, the files and data
  involved, dependencies, failure cases, and its real status and verification
  state.

### Two output modes over one source

Both modes render the **same** `.vibekb/` content through the **same** templates:

- **Mode A — Dynamic guide (`guide/`):** a plain PHP 8.2 app that reads
  `.vibekb/` live. Runs on cPanel shared hosting or locally; works in a
  subfolder with no rewrite rules; no build step.
- **Mode B — Static snapshot (`/docs`):** `php tools/generate-static.php`
  renders the guide into a self-contained static site for GitHub Pages or any
  static host — no PHP, no CDN, no network. It is a **snapshot of the source
  commit at generation time and does not update itself**; re-run the generator
  to refresh it. `/docs` is generated output, clearly labelled as such; the
  source of truth remains `.vibekb/`.

Every page states its **provenance** — which source commit was analysed, when
the analysis was generated, the verification scope, and that it is not
auto-updating.

### The V1 views

Overview · Functionality Index · Functionality Detail · How It Works ·
**Diagrams** · Data & Storage · Files That Matter · Current AI Work · Changes ·
Why It Works This Way · AI Handoff · Reference · Search.

**Diagrams** are first-class, source-grounded SVG records in `.vibekb/diagrams/`
(accessible `<title>`/`<desc>`, inferred paths labelled) that cross-link to the
functionality and warnings they explain. They can be **explainable**: a
repository-owned topology (`diagrams/topology/<id>.json`) gives every node a
purpose, every edge a concrete mechanism, and every file a reason, with external
source links as the terminal "show me the implementation" — usable without
JavaScript in both output modes.

### VibeKB is self-hosted

The active `.vibekb/` content models **VibeKB itself** — VibeKB explaining VibeKB.
Every view is demonstrated with VibeKB's own source-grounded functionality (the
content loader, the guide renderer, the static generator, the validator, the
explainable-diagram system, and the self-maintenance CLI). Open the guide, or the
published `/docs`, to see the product explain itself.

Bundled models of **other** applications — the **SousMeow** example and the StopPR
field-test audit — live under `examples/`. They are demonstrations and fixtures,
**not** the active model, and never to be confused with the current state of
VibeKB. Preview or validate one with
`VIBEKB_CONTENT_ROOT=examples/sousmeow/.vibekb php -S localhost:8080 -t .` or
`php tools/vibekb.php validate examples/sousmeow/.vibekb`.

### Self-maintenance CLI

`tools/vibekb.php` is the one entry point agents use to keep the model in step
with the code:

```bash
php tools/vibekb.php status      # session start: provenance, current work, next action, drift
php tools/vibekb.php affected --since <ref>   # changed files → likely affected functionality
php tools/vibekb.php bootstrap   # verify/repair the .vibekb/ workspace (git-init for VibeKB)
php tools/vibekb.php check       # validate + broken references + drift + /docs sync
php tools/vibekb.php generate    # regenerate /docs
```

It **detects** changes mechanically (git diff, path existence, a render-and-diff)
and is explicit that **interpreting** a change into the model is an agent's job —
VibeKB never claims to auto-update.

### The `vibekb` developer CLI (Go front-end)

VibeKB is growing a single, portable developer command. The `vibekb` binary
(`cmd/vibekb`, `internal/*`) is a Go front-end: it runs environment diagnostics
natively and **delegates every model command to the PHP core above** — it does not
re-implement the model loader, so there is still exactly one implementation of
parsing, validation, and generation.

```bash
go build -o vibekb ./cmd/vibekb   # build it today (Go 1.24+)
./vibekb doctor                   # native: is PHP 8.2+, git, a workspace present?
./vibekb check                    # delegates to php tools/vibekb.php check
```

Delegated commands still need PHP 8.2+ present, and `vibekb doctor` says so
plainly. A one-command install path (`brew` / `winget` / `curl`) is on the
roadmap. See **[ARCHITECTURE.md](./ARCHITECTURE.md)** for the assessment behind
this direction and the staged plan.

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

## Generate the static snapshot

```bash
php tools/validate.php           # gate: no content errors
php tools/generate-static.php    # renders /docs from .vibekb/
```

Then open `docs/index.html` (or publish `/docs` via GitHub Pages:
Settings → Pages → branch → `/docs`). The output uses relative links, so it
works at the web root or under a repository subpath. It does not require PHP,
a database, a CDN, or a network connection.

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
  diagrams/       index.json + records/ + assets/ (SVGs) + topology/ (explainable)
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

- **Not automatic.** VibeKB detects that code changed; it does not understand what
  a change *means*. Records are written and maintained by you and your coding
  agent following the workflow in [MAINTENANCE.md](./MAINTENANCE.md). `updates_automatically`
  is `false` and stays that way.
- **Affected-functionality discovery is a heuristic** built from the `files[]`
  back-links in the model — a changed file with no back-link is surfaced as
  "unmapped," never silently ignored, but the mapping is not assumed perfect.
- **Cursor discovery** is provided via `.cursor/rules/` and `AGENTS.md`; it is
  `inferred` that a fresh Cursor session follows it, not runtime-verified here.
- **The bundled examples** (SousMeow, StopPR) are read-only snapshots of separate
  apps and can drift from their sources over time.
- The Markdown renderer supports a pragmatic subset (headings, lists, tables,
  code, emphasis, links, blockquotes) — not full CommonMark.

## For AI agents

Start with `php tools/vibekb.php status`. The canonical, repository-owned workflow
lives in [CLAUDE.md](./CLAUDE.md); [AGENTS.md](./AGENTS.md) and
`.cursor/rules/vibekb.mdc` are thin pointers to it. Read
[MAINTENANCE.md](./MAINTENANCE.md) for the detailed change lifecycle,
[INSTALLER.md](./INSTALLER.md) to install/upgrade VibeKB, and
[INITIALIZE.md](./INITIALIZE.md) to build the model in a freshly installed
repository. A new agent should be able to discover and follow the workflow
without being handed a giant prompt.
