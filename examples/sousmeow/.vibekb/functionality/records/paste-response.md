---
id: paste-response
type: functionality
title: Paste a response
area: cookbook-runner
summary: Stores a pasted AI response (or the seeded example) as a new immutable artifact version after sanitising untrusted input.
status: implemented
verification: verified-from-source
user_facing: true
trigger: A user posts to ".../paste" or ".../example" on a Recipe step.
updated: 2026-07-16
tags: [runner, artifact, write, security]
files: [app/Controllers/RunnerController.php, app/Models/Artifact.php, app/Models/Project.php]
reads: [artifacts, artifact_versions]
writes: [artifacts, artifact_versions]
config: []
depends_on: [run-recipe]
related_memory: [decision:immutable-artifact-versions, warning:pasted-response-is-untrusted, discovery:demo-mode-labeling]
---

## In one sentence

Paste your AI's answer and it is saved as a new, immutable version — or, in Demo
Mode, paste the recipe's seeded example instead.

## Current behavior

`paste()` requires verification, cleans the content
(`cleanContent()`: normalises newlines, strips control characters except
newline/tab, trims, and requires 20–200,000 chars), and stores it as a new
version with source `pasted`. `pasteExample()` does the same with the recipe's
seeded `example_response`, source `example`, and a message that marks it as
sample data. Both touch the project's `updated_at`.

## Step-by-step flow

1. User posts their response to `/projects/{id}/run/{position}/paste`.
2. `cleanContent()` sanitises it; empty/too-short/too-long → error redirect.
3. `Artifact::addVersion(..., 'pasted')` appends an immutable version.
4. `Project::touch()` updates recency; redirect back to the step (now `review`).

## Implementation map

- `app/Controllers/RunnerController.php` — `paste()`, `pasteExample()`,
  `cleanContent()`.
- `app/Models/Artifact.php` — `addVersion()` (new `version_no`, never mutates).

## Data used

- **Inputs:** the pasted text (or seeded example).
- **Writes:** an `artifacts` row (if first) and a new `artifact_versions` row.

## Dependencies

An active Recipe step (`run-recipe`).

## Dependents

**Review Quality Checks** and **Approve & version** act on the pasted version.

## Failure cases

- Empty / < 20 / > 200,000 chars → rejected with a message; nothing stored.
- Example paste on a recipe with no example → error message.

## Current state

- **Status:** implemented. **Verification:** verified from source (`paste`,
  `pasteExample`, `cleanContent` traced).

## Use caution

Pasted content is untrusted input. It is sanitised on store and escaped on
render (`SafeText`) — never relax either. See the `pasted-response-is-untrusted`
warning.

## Why it works this way

Every paste is a new version, never an overwrite, so the full history (paste /
example / edited / restored) is always recoverable — see the
`immutable-artifact-versions` decision. Demo Mode examples are always labelled
as sample data (`demo-mode-labeling`).

## Related functionality

- Run a Recipe
- Review Quality Checks
- Approve & version an artifact
