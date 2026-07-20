---
id: review-quality-checks
type: functionality
title: Review Quality Checks
area: artifacts-quality
summary: The human review step — confirm each Quality Check against the exact version, aided by structural evidence parsed from the response but never graded automatically.
status: implemented
verification: verified-from-source
user_facing: true
trigger: A user toggles a check on ".../checks" for a Recipe step.
updated: 2026-07-16
tags: [quality, review, write]
files: [app/Controllers/RunnerController.php, app/Services/ResponseParser.php, app/Services/OutputContract.php, app/Models/Recipe.php, app/Models/Artifact.php]
reads: [recipe_checks, artifact_versions, artifact_checks]
writes: [artifact_checks]
config: []
depends_on: [paste-response]
related_memory: [decision:immutable-artifact-versions]
---

## In one sentence

You confirm each Quality Check against the exact text you are looking at;
SousMeow shows structural evidence but never decides for you.

## User experience

Each check is a card. When the Recipe has an output contract, the card shows
whether the expected section is present (found / missing / manual) with the
parsed evidence — but confirming is always your explicit judgement.

## Current behavior

`toggleCheck()` requires verification and always targets the **latest** version
(checking a box means "I read THIS text"). It accepts either a single
`check_id`+`confirmed` toggle (answered as JSON for `fetch()`) or a plain
`checks[]` form (answered by redirect). Confirmations are stored in
`artifact_checks` keyed by version, so a new version starts unconfirmed by
construction. Evidence comes from `ResponseParser::parse()` against the recipe's
`output_contract`; recipes without a contract fall back to manual full-response
review and parsing never blocks anything.

## Step-by-step flow

1. User posts a toggle to `/projects/{id}/run/{position}/checks`.
2. The check id is validated against the recipe's checks.
3. `Artifact::setCheck(versionId, checkId, on)` records or clears it.
4. The response reports `confirmed/total` and whether approval is now allowed.

## Implementation map

- `app/Controllers/RunnerController.php` — `toggleCheck()`, `checkResponse()`.
- `app/Services/ResponseParser.php` — section parsing + `checkStatus()`.
- `app/Services/OutputContract.php` — the contract shape.

## Data used

- **Reads:** `recipe_checks`, the latest `artifact_versions` row.
- **Writes:** `artifact_checks` (unique per version+check).

## Dependencies

A pasted version exists (`paste-response`).

## Dependents

**Approve & version** — approval requires every check confirmed.

## Failure cases

- No version yet → "paste a response before reviewing".
- Unknown check id → rejected.

## Current state

- **Status:** implemented. **Verification:** verified from source
  (`toggleCheck` + parser usage traced; the `ResponseParser` internals were read
  at the call boundary).

## Use caution

Confirmations bind to a specific version. Never migrate a confirmation to a new
version — that would silently mark unreviewed text as reviewed.

## Why it works this way

The product's promise is human review, not automated grading; evidence assists
judgement, it does not replace it.

## Related functionality

- Paste a response
- Approve & version an artifact
