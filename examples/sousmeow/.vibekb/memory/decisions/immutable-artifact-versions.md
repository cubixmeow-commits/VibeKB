---
id: immutable-artifact-versions
type: decision
title: Artifacts are immutable, append-only versions
summary: Every paste, example, edit, and restore creates a new version; nothing is ever overwritten, and Quality Check confirmations bind to a specific version.
status: accepted
verification: verified-from-source
updated: 2026-07-16
functionality: [paste-response, approve-and-version, review-quality-checks, build-prompt]
files: [app/Models/Artifact.php, app/Controllers/RunnerController.php, database/schema.sqlite.sql]
tags: [architecture, data-integrity, review]
---

## Context

The product's promise is trustworthy, reviewed output. That requires a reliable
history and a guarantee that "I reviewed this" refers to an exact text.

## Decision

`artifact_versions` is append-only with a `source` (`pasted` / `example` /
`edited` / `restored`). Edits and restores add versions; they never mutate.
`artifact_checks` records a confirmed check against a specific `version_id`, so a
new version starts unconfirmed by construction, and approval targets the latest
version.

## Reason

- A new version can never inherit an old version's confirmations, so unreviewed
  text can never appear approved.
- Only the immutable approved version chains into later prompts and the export,
  so the workflow is reproducible.

## Consequences

- History accumulates (small text rows); this is intentional.
- Approval requires re-confirming checks after any new version.

## Current status

Active and central. Do not add an in-place update path for artifact content.
