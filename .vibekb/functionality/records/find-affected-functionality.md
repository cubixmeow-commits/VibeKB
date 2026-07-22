---
id: find-affected-functionality
type: functionality
title: Find affected functionality
area: agent-workflow
summary: Given a set of changed files, `php tools/vibekb.php affected` lists the functionality records, important-files entries, and diagram topologies that reference them — turning "six files changed" into "here is the knowledge that may now be wrong."
status: implemented
verification: verified-manually
user_facing: true
trigger: A coding agent runs `php tools/vibekb.php affected <file>...` or `--since <ref>`.
updated: 2026-07-22
tags: [cli, impact-analysis, self-maintenance]
files: [tools/vibekb.php, guide/lib/Content.php]
reads: [.vibekb/functionality, .vibekb/files/important-files.json, .vibekb/diagrams/topology]
writes: []
depends_on: [resolve-relationships]
related_memory: [decision:functionality-first-not-files, warning:model-can-drift-from-code]
---

## In one sentence

It reverses the `files[]` back-links the model already carries: for each changed
path, which functionality claims to be implemented there, which important-file
entry describes it, and which diagram nodes/edges point at it.

## User experience

`php tools/vibekb.php affected app/x.php guide/lib/Content.php` prints, per file,
the functionality records that list it, the important-files entry, and any
diagram topology file references — or "no record references this file (may need a
new or updated record)" when nothing matches. With `--since <ref>` it derives the
file list from git instead of arguments.

## Implementation map

- `tools/vibekb.php` — the `affected` subcommand.
- `guide/lib/Content.php` — the `files[]` back-links this reads.

## Failure cases

- A file no record references is reported as unmapped, not dropped.
- A path typo simply yields "no record references this file."

## Use caution

This is exact string matching against recorded paths, so it is only as complete
as the model's `files[]` lists. It is a starting point for the agent's judgement,
not a guarantee that the listed records are the only ones affected.

## Why it works this way

Functionality — not files — is VibeKB's unit, but code changes arrive as files.
This is the bridge: it puts the agent in front of the *functionality* a file
change might invalidate, which is where the reconciliation work actually happens.
