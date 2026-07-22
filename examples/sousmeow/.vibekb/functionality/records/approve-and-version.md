---
id: approve-and-version
type: functionality
title: Approve & version an artifact
area: artifacts-quality
summary: The approval gate (every check confirmed) plus the full immutable version toolkit — edit, reopen, restore — that keeps history intact.
status: implemented
verification: verified-from-source
user_facing: true
trigger: A user posts to ".../approve", ".../reopen", ".../edit", or ".../restore".
updated: 2026-07-16
tags: [quality, approval, versioning, write]
files: [app/Controllers/RunnerController.php, app/Models/Artifact.php, app/Models/Project.php]
reads: [artifacts, artifact_versions, artifact_checks, recipe_checks, recipes]
writes: [artifacts, artifact_versions, projects]
config: []
depends_on: [review-quality-checks]
related_memory: [decision:immutable-artifact-versions]
---

## In one sentence

Approve locks the current version — but only once every Quality Check is
confirmed — and you can always edit, restore an older version, or reopen without
losing history.

## Current behavior

`approve()` refuses unless the count of confirmed checks equals the number of
checks, then approves the latest version and, if every Recipe is now approved,
marks the project complete and sends the user to export (otherwise to the next
Recipe). `reopen()` withdraws an approval. `saveEdit()` stores an edited copy as
a **new** version (`edited`), never touching the original, and no-ops if the
text is unchanged. `restore()` brings an older version back as a new version
(`restored`). Every version is immutable; history is never destroyed.

## Step-by-step flow (approve)

1. User posts to `/projects/{id}/run/{position}/approve`.
2. Confirmed-check count is compared to total checks.
3. Fewer confirmed → notice, no approval.
4. All confirmed → `Artifact::approve()` locks the version.
5. `markCompleteIfDone()` → export, or advance to the next Recipe.

## Implementation map

- `app/Controllers/RunnerController.php` — `approve`, `reopen`, `saveEdit`,
  `restore`.
- `app/Models/Artifact.php` — `approve`, `reopen`, `addVersion`, `version`.

## Data used

- **Reads:** checks and versions.
- **Writes:** `artifacts.status`/`approved_version_id`; new `artifact_versions`;
  `projects` recency/completion.

## Dependencies

Every check confirmed (`review-quality-checks`).

## Dependents

Approved Artifacts chain into later prompts (`build-prompt`) and populate the
**Project Kit**.

## Failure cases

- Not all checks confirmed → approval refused with a notice.
- Nothing pasted yet → "nothing to approve".
- Edit identical to current → no-op notice.

## Current state

- **Status:** implemented. **Verification:** verified from source (all four
  transitions traced).

## Use caution

The all-checks gate is the core discipline of the product; never bypass it.
Versions are append-only — edit/restore add versions, they do not mutate.

## Why it works this way

Immutable versioning plus an explicit approval gate is what makes the output
trustworthy and the chaining reliable — see `immutable-artifact-versions`.

## Related functionality

- Review Quality Checks
- Build the prompt
- Export the Project Kit
