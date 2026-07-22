---
id: functionality-first-not-files
type: decision
title: Functionality is the primary unit, not files
summary: VibeKB organizes understanding around what the software does (behaviours), with files, memory, and diagrams linking back to functionality — never the reverse.
status: accepted
verification: verified-from-source
updated: 2026-07-22
functionality: [load-living-model, resolve-relationships, find-affected-functionality, render-explainable-diagrams]
files: [guide/lib/Content.php, PRODUCT.md]
tags: [architecture, product]
---

## Context

The obvious way to explain a codebase is a file tree or a call graph. But a file
tree tells you where code is, not what the software *does*, and it goes stale as
files move. Vibe coders ask "what does my app do and does it still work?", not
"what is in `app/Services`?".

## Decision

Functionality — the behaviours the software performs — is VibeKB's primary
record type. Files (`important-files.json`), memory, and diagram nodes all carry
`functionality[]` back-links; the loader derives the reverse relationships.

## Alternatives considered

- **File-first / directory-mirroring model** — rejected: it becomes a code
  browser and drifts with every refactor.
- **Decision-log / ADR-first model** — rejected: that is repository memory, which
  exists to protect the functionality explanation, not replace it.

## Reason

A functionality record is readable without opening the source and survives
refactors; a file list does neither. It is also what makes "find affected
functionality" meaningful — a file change maps to the behaviours it may break.

## Consequences

- Every memory, file, and diagram element links to functionality.
- The drift check reports changed files *as* the functionality they likely
  affect.
- Reversing this would turn VibeKB into a code browser — explicitly out of scope.
