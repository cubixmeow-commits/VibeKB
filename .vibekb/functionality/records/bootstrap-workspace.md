---
id: bootstrap-workspace
type: functionality
title: Bootstrap the VibeKB workspace
area: integration
summary: A deterministic `php tools/vibekb.php bootstrap` command (and the shared `tools/lib/Starter.php` library) that creates or repairs a `.vibekb/` workspace — every required directory and starter file — without inspecting source, inventing functionality, or overwriting content. "git init" for VibeKB.
status: implemented
verification: verified-from-source
user_facing: true
trigger: A developer runs `php tools/vibekb.php bootstrap` (or the installer calls the same library) against a repository.
updated: 2026-07-22
tags: [integration, scaffolding, cli, repair]
files: [tools/lib/Starter.php, tools/vibekb.php]
reads: []
writes: [.vibekb]
config: []
depends_on: []
related_memory: [decision:installer-prepares-agent-interprets, decision:installer-template-not-duplicated-tree]
---

## In one sentence

`bootstrap` guarantees a valid, empty `.vibekb/` skeleton exists — creating any
missing directories and starter files and leaving all existing content untouched
— so the workspace is always well-formed before an agent builds the model.

## User experience

Running `php tools/vibekb.php bootstrap` prints what it created versus kept and
ends with a clear OK or an explanation of what is still missing. `--dry-run`
reports the same plan without writing anything.

## Current behavior

`vibekb_verify_workspace()` reports which required directories and starter files
are missing; `vibekb_scaffold_workspace()` creates the missing ones and writes any
missing starter files, never overwriting an existing file (so it is safe on a
fresh, partial, or damaged workspace). The starter definition —
`vibekb_starter_dirs()` and `vibekb_starter_files()` — is the single source of
truth, also used by `install.php`, so the two can never disagree about what a
fresh model contains. The starter files are explicit placeholders that tell an
agent what to write; none claims the target software does anything.

## Step-by-step flow

1. Verify the workspace against the starter definition.
2. Create any missing directories.
3. Write any missing starter files; keep every existing file.
4. Re-verify and report OK or what remains missing.

## Implementation map

- `tools/lib/Starter.php` — the starter definition plus verify/scaffold helpers.
- `tools/vibekb.php` — the `bootstrap` command that reports the scaffold result.

## Data used

- **Writes:** only into `.vibekb/` (directories and missing starter files).
- **Reads:** nothing of the application's source — it never inspects code.

## Failure cases

- A directory or file that cannot be created is reported as an error; existing
  content is never at risk because nothing is overwritten.

## Safe to change

Adding a required directory or starter file to `tools/lib/Starter.php` updates
both bootstrap and the installer at once. Keep every starter file valid so the
scaffolded model passes `php tools/vibekb.php check`.

## Use caution

Bootstrap must never generate functionality, invent diagrams, inspect source, or
write documentation about the software. It only lays down empty scaffolding — the
honest boundary that separates preparing the workspace from interpreting the
software.

## Why it works this way

A deterministic scaffold/repair is exactly the mechanical work a script can own
without faking understanding. Sharing one definition with the installer keeps the
starter model consistent and makes a damaged workspace trivially recoverable.
