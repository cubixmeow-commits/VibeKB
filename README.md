# VibeKB

**A repository-native knowledge layer for AI-assisted software development.**

VibeKB keeps a living, structured explanation of what your software actually does
— stored inside the repository, versioned with your code, and shared by every
developer and every AI session that touches the project.

- **Live guide:** <https://iainreid.dev/vibekb/guide/>
- **Real example:** [Stoppr, a production app made understandable](#example-stoppr)
- **Works with:** Claude Code · Cursor · Codex · Windsurf · Copilot · Gemini CLI

---

## Why it exists

AI changed how software gets built. VibeKB exists because that created two
problems at the same time.

### 1. AI builds faster than you can understand

An agent can add a feature across six files in a minute. The app works — but your
mental model of *how* it works falls behind a little more with every session.
Soon you're guessing which files matter and hoping the next edit doesn't break
something you can't see.

VibeKB generates a living knowledge base that explains, in plain language:

- what the software currently does
- how it works and how the systems connect
- which files matter
- what AI is changing right now
- what is unfinished or risky
- where the next person should begin

**This is the understanding layer.**

### 2. Every AI session starts from zero

Claude Code, Cursor, Codex, Gemini — each new conversation begins with almost no
memory of your project. You re-explain the architecture, the decisions, and the
landmines again and again. The context you built up last week is gone.

VibeKB stores that knowledge *inside the repository itself*. It's committed with
your code, so the next developer — and the next AI session — inherits it instead
of rebuilding it.

**This is the persistent memory layer.**

### They're the same thing

Persistent memory is what makes understanding survive. A guide that resets every
session isn't understanding — it's a chat log. Because VibeKB's knowledge lives
in the repo and is maintained as the code changes, the explanation stays accurate
and every future session starts already knowing the project.

> AI helped you build it. VibeKB helps you — and every future session —
> understand it.

---

## What VibeKB creates

VibeKB turns your repository into a browsable guide. The pieces:

| | |
|---|---|
| **Software Guide** | A readable overview of what the application does, rendered from the model. |
| **Functionality Map** | An interactive map of functional areas and how they connect — understanding at a glance. |
| **Repository Memory** | Decisions, constraints, warnings, and discoveries, each linked to the behaviour it explains. |
| **Important Files** | The files that actually matter, and why — so you stop guessing. |
| **Current AI Work** | What an agent is changing right now: the goal, the blast radius, the risks. |
| **Diagrams** | Source-grounded, explainable diagrams: every node has a purpose, every edge a mechanism. |
| **Handoff** | The exact next step, written for whoever (or whatever) picks the project up next. |

Every view states its honest status — implemented, partial, or unknown — and how
each claim was verified. See it on VibeKB's own model in the
[live guide](https://iainreid.dev/vibekb/guide/).

---

## Start with VibeKB

The best time to adopt VibeKB is on **day one** of a new project. Capture
architecture, decisions, functionality, and current work from the first commit,
and the knowledge base grows alongside the code instead of being reconstructed
later from a codebase nobody fully remembers.

```
Create repository
      ↓
Initialize VibeKB
      ↓
Build with AI
      ↓
Repository memory grows
      ↓
Generated guide evolves
      ↓
Future AI sessions inherit the knowledge
```

Install the CLI, then point it at your project:

```bash
curl -fsSL https://iainreid.dev/vibekb/install.sh | sh   # macOS / Linux
cd /path/to/your/project
vibekb install .
```

Then open the project in your coding agent and ask it to build the first model:

> Build the first VibeKB model for this repository using
> `prompts/INTEGRATE_VIBEKB.md`.

That's it — the agent reads your source and writes the model; from then on it
stays in step as you build.

---

## Already have a project?

Existing repositories are fully supported. VibeKB analyzes the software you
already have and generates the same understanding — read-only, without touching
your application code.

```
Clone project
      ↓
Install VibeKB          vibekb install .
      ↓
Agent analyzes the existing software (read-only)
      ↓
Generate the understanding
      ↓
Continue building — now with persistent memory
```

The install and first prompt are identical to a new project; the only difference
is that the agent is modelling code that already exists rather than code you're
about to write.

---

## How it works

VibeKB is **not** another coding agent, and it does **not** understand your code
automatically. It gives the agents you already use a place to record and read
durable knowledge. The moving parts are deliberately simple:

- **`.vibekb/`** — the model. Plain Markdown plus small JSON manifests,
  human-readable and versioned with your code. This is the single source of
  truth. No database.
- **The generated guide** — the same model rendered as a browsable site. A
  dynamic PHP guide reads `.vibekb/` live; a static snapshot renders it to
  `/docs` for GitHub Pages or any static host. Both are built from the one model,
  so they can't disagree.
- **Provenance, not magic** — every page states the source commit analyzed, when
  it was generated, and that it does **not** update itself. VibeKB detects that
  code changed; interpreting the change is the agent's job.
- **Repository safety** — VibeKB owns only the `.vibekb/` folder. It never
  modifies your application code and never replaces your `README`, `AGENTS.md`,
  or `CLAUDE.md`; it integrates with shared files through a single clearly-marked
  block. Preview any install with `vibekb install --dry-run .` and reverse it
  with `vibekb uninstall .`. See
  [docs/REPOSITORY_SAFETY.md](./docs/REPOSITORY_SAFETY.md).

<a id="example-stoppr"></a>

## Example: Stoppr

[Stoppr](https://github.com/cubixmeow-commits/VibeKB-stoppr) is a production
Flutter app (iOS & Android) — a sugar-reduction companion with streaks, panic
interventions, nutrition tools, and subscription gating. AI helped build it.
VibeKB made the resulting codebase understandable.

A coding agent modelled it **read-only** — no app code changed. The result:

- **35** functionality records across **10** functional areas
- **all verified from source** (31 implemented, 4 partial)
- **6** systems · **31** files that matter
- **6** real landmines surfaced before anyone edits — placeholder OAuth client
  IDs and API keys, placeholder Superwall placements, an `.env.local` the app
  doesn't load, incomplete Android purchase wiring, a placeholder iOS widget
  app-group ID

*(A clean VibeKB re-analysis at commit `2edc099`; a static snapshot that does not
update itself.)* Browse the published guide:
<https://cubixmeow-commits.github.io/VibeKB-stoppr/>

---

## Features

- **Functionality-first.** Organized around what the software *does*, not around
  files or an AI activity log.
- **Honest by design.** Every record distinguishes intended, implemented, and
  verified behaviour, and marks uncertainty instead of hiding it.
- **Interactive Functionality Map** — areas, capabilities, and real relationships,
  usable without JavaScript.
- **Explainable diagrams** — source-grounded, with a purpose per node and a
  mechanism per edge.
- **Two output modes, one source** — a live PHP guide and a static snapshot for
  GitHub Pages, rendered from the same `.vibekb/`.
- **Self-maintenance CLI** — detects drift between the model and the code so the
  explanation can't quietly rot.
- **Repository-safe** — owns only `.vibekb/`, previewable, reversible.
- **No lock-in infrastructure** — no database, no external/AI API for rendering,
  no vendor account, no build step.
- **Self-hosted** — the active `.vibekb/` model in this repository describes
  VibeKB itself, so the product demonstrates itself.

---

## Installation

**Install the CLI** (macOS / Linux):

```bash
curl -fsSL https://iainreid.dev/vibekb/install.sh | sh
```

The installer detects your platform, downloads the matching binary from GitHub
Releases, and places `vibekb` on your `PATH`.

**Windows:** download `vibekb-windows-amd64.exe` (or `-arm64`) from
[GitHub Releases](https://github.com/cubixmeow-commits/VibeKB/releases/latest),
rename it to `vibekb.exe`, and put it on your `PATH`.

**Add it to a repository:**

```bash
vibekb install .                 # scaffolds .vibekb/ and the runtime
vibekb install --dry-run .       # preview every change first; writes nothing
```

**Requirements:** installing needs no PHP. **PHP 8.2+** is required only to run
the installed guide and the model commands. No database, no AI API, no Docker, no
build step. Homebrew and Winget are on the roadmap.

**Build from source** (Go 1.24+):

```bash
git clone https://github.com/cubixmeow-commits/VibeKB.git
cd VibeKB
go build -o vibekb ./cmd/vibekb
./vibekb install /path/to/your/project
```

See [INSTALLER.md](./INSTALLER.md) for the full flow and
[RELEASE.md](./RELEASE.md) for publishing binaries.

---

## Architecture

One content model, two renderers, honest provenance throughout.

- **Content model — `.vibekb/`.** The single source of truth: `project/`,
  `functionality/`, `system/`, `files/`, `diagrams/`, `memory/`, and `work/`,
  plus a `manifest.json`. Markdown with front matter and small JSON manifests.
- **Mode A — dynamic guide (`guide/`).** A plain PHP 8.2 app that reads `.vibekb/`
  live, routes by query string, and needs no rewrite rules and no build step.
- **Mode B — static snapshot (`/docs`).** `php tools/generate-static.php` renders
  the same templates into a self-contained static site with subpath-safe links.
  It's generated output, clearly labelled; `.vibekb/` remains the source of truth.
- **Self-maintenance CLI (`tools/vibekb.php`).** Detects drift mechanically
  (broken references, changed files, stale snapshot) and is explicit that
  *interpreting* a change is an agent's job. VibeKB never claims to auto-update.

Full detail in [ARCHITECTURE.md](./ARCHITECTURE.md); the content model and its
validation rules are in [SCHEMA.md](./SCHEMA.md).

---

## CLI

The portable `vibekb` binary is the developer front door. It installs natively
(no PHP) and delegates model commands to the PHP core, so there's exactly one
implementation of parsing, validation, and generation.

```bash
vibekb install ../my-project     # native: embeds + copies the runtime, no PHP
vibekb doctor                    # native: PHP 8.2+, git, workspace present?
vibekb version                   # version / commit / built / platform
vibekb check                     # delegates to the PHP core (needs PHP 8.2+)
vibekb uninstall .               # ownership-aware removal
```

The repository-owned maintenance CLI is what agents drive through the lifecycle:

```bash
php tools/vibekb.php status                  # session start: provenance, work, next action, drift
php tools/vibekb.php affected --since <ref>  # changed files → likely-affected functionality
php tools/vibekb.php check                   # validate + broken references + drift + /docs sync
php tools/vibekb.php generate                # regenerate /docs
```

---

## Development

Run the guide locally:

```bash
VIBEKB_DEV=1 php -S localhost:8080 -t .
# http://localhost:8080/            homepage
# http://localhost:8080/guide/      the software guide
```

Regenerate the static snapshot:

```bash
php tools/validate.php           # gate: no content errors
php tools/generate-static.php    # renders /docs from .vibekb/
```

VibeKB is self-hosted: the active `.vibekb/` describes VibeKB itself, so changing
the code means keeping its model in step. The canonical workflow for agents and
contributors lives in [CLAUDE.md](./CLAUDE.md); [MAINTENANCE.md](./MAINTENANCE.md)
covers the change lifecycle. Bundled example models of other applications live
under `examples/` and are never the active model.

---

## Technical documentation

| Document | What it covers |
|---|---|
| [PRODUCT.md](./PRODUCT.md) | The product definition and promise |
| [ARCHITECTURE.md](./ARCHITECTURE.md) | How the system is built |
| [SCHEMA.md](./SCHEMA.md) | The content model, statuses, and validation |
| [INSTALLER.md](./INSTALLER.md) | Install / upgrade / uninstall in detail |
| [INITIALIZE.md](./INITIALIZE.md) | Building the first model in a repository |
| [MAINTENANCE.md](./MAINTENANCE.md) | Keeping the model in step with the code |
| [DEPLOYMENT.md](./DEPLOYMENT.md) | Shared hosting, cPanel, and static hosting |
| [RELEASE.md](./RELEASE.md) | Publishing binaries |
| [docs/REPOSITORY_SAFETY.md](./docs/REPOSITORY_SAFETY.md) | What VibeKB will and won't touch |
| [CHANGELOG.md](./CHANGELOG.md) | What changed |

---

## Current limitations

- **Not automatic.** VibeKB detects that code changed; it does not understand what
  a change *means*. You and your coding agent maintain the model. `updates_automatically`
  is `false` and stays that way.
- **Affected-functionality discovery is a heuristic** built from the file
  back-links in the model — an unmapped changed file is surfaced, never silently
  ignored, but the mapping isn't assumed perfect.
- **Bundled examples** (SousMeow, Stoppr) are read-only snapshots and can drift
  from their sources over time.
