# INITIALIZE.md — Adding VibeKB to another repository

This is a reusable process for an AI agent to initialize VibeKB in a real
project. The goal is to produce a **living software model** that explains what
the application currently does — honestly separating intended, implemented, and
verified behaviour.

Copy `guide/` (the app), `PRODUCT.md`, `CLAUDE.md`/`AGENTS.md`, and `SCHEMA.md`
into the target repo, then build a fresh `.vibekb/` following the steps below.

## 1. Inspect the repository

- List the repository and read existing docs (README, etc.).
- Identify the language, framework (if any), entry points, and how it runs.
- Find where data is stored (database, files, sessions, caches).
- Find configuration (env vars, config files) and deployment (CI, host).

## 2. Identify what the application currently does

Trace the real behaviour, not the aspirations:

- What are the primary user workflows? Walk each from trigger to result.
- What system/background behaviour exists (init, migrations, scheduled work)?
- Which files implement each behaviour?
- What does each behaviour read and write?

## 3. Separate intended, implemented, and verified

For every behaviour, decide:
- **Status**: implemented, partial, planned, experimental, disabled,
  deprecated, broken, or unknown.
- **Verification**: verified-by-test, verified-manually, verified-from-source,
  inferred-from-source, reported-by-developer, or not-verified.

Never upgrade a status you haven't confirmed. Mark uncertainty honestly.

## 4. Create the initial content

Following `SCHEMA.md`, create:

- `project/identity.md`, `intent.md`, `current-state.md`, `constraints.md`.
- `functionality/index.json` + one record per meaningful behaviour. Start with
  the primary workflow; cover the real behaviours, not every function.
- `system/` docs: at minimum `mental-model.md`, `components.md`,
  `request-flow.md`, `storage.md`, and `deployment.md`.
- `files/important-files.json` — only the files worth understanding, with
  evidence-based safety levels.
- `memory/` — capture the decisions, constraints, assumptions, warnings, and
  discoveries you can actually establish. Do not invent history.
- `work/current.md` — the current AI context (initialization itself, or the
  first real task).
- `work/handoff.md` — a first honest handoff.

## 5. Mark uncertainty and ask only essential questions

Where you cannot verify something from the source, mark it `unknown` /
`not-verified` and add an assumption record with a `next_check`. Ask the project
owner **only** the questions you cannot answer from the code and that materially
change the model (e.g. "is this half-built feature meant to ship or be removed?").

## 6. Validate

Run `php -l` on `guide/`, load `guide/?view=reference`, and confirm there are no
validation errors and no broken relationships before handing off.

## What "done" looks like

Someone can open `guide/`, read the overview, and correctly understand what the
application does right now — including what is unfinished, risky, or unverified.
