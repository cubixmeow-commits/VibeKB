---
id: run-recipe
type: functionality
title: Run a Recipe
area: cookbook-runner
summary: The product's core loop — one Recipe step rendered as one of three durable states (gather, review, approved), with strict server-side step ordering.
status: implemented
verification: verified-from-source
user_facing: true
trigger: A user opens "/projects/{id}/run/{position}".
updated: 2026-07-16
tags: [runner, core-loop, flagship]
files: [app/Controllers/RunnerController.php, app/Views/runner/step.php, app/Models/Recipe.php, app/Models/Artifact.php, app/Models/PantryField.php, app/Models/Project.php, app/Services/PromptBuilder.php, app/Services/ResponseParser.php]
reads: [projects, recipes, recipe_checks, pantry_fields, pantry_values, artifacts, artifact_versions, artifact_checks]
writes: []
config: []
depends_on: [fill-pantry, build-prompt]
related_memory: [decision:immutable-artifact-versions, warning:pasted-response-is-untrusted]
---

## In one sentence

Each step of the Cookbook shows exactly one durable state — gather a response,
review it, or done — and steps unlock strictly in order.

## User experience

The step page presents a three-screen wizard while gathering (understand →
prompt → paste): read what the step does, copy the built prompt (with your
ingredients highlighted), run it in your own AI, and paste the answer. Once a
response exists, the page becomes a review screen; once approved, it locks.

## Current behavior

`RunnerController::step()` loads the project (owner-scoped), the recipe, and the
artifact. The durable **state** is derived from data, not the URL: `approved`
if the artifact is approved, `review` if a latest version exists, else
`gather`. A `?stage=` query drives the gather wizard but is clamped server-side
and never stored, so a refresh always lands on the correct state. It builds the
prompt (`PromptBuilder`), parses the latest version against the recipe's output
contract (`ResponseParser`) to produce per-check review cards, and computes the
"ingredients used" panel.

## Step-by-step flow

1. User opens `/projects/{id}/run/{position}`.
2. `load()` enforces ownership, that the Pantry is saved, that the recipe
   exists and is runnable, and **that every earlier recipe is approved** (steps
   unlock in order).
3. The durable state is computed from the artifact (gather / review / approved).
4. `PromptBuilder::build()` assembles the prompt; the Pantry ingredients it used
   are summarised.
5. If the recipe has an output contract, `ResponseParser` builds structural
   evidence for each Quality Check.
6. `runner/step` renders the correct state.

## Implementation map

- `app/Controllers/RunnerController.php` — `step()` and `load()` (ordering gate).
- `app/Services/PromptBuilder.php` — the prompt (see **Build the prompt**).
- `app/Services/ResponseParser.php` — structural evidence for checks.
- `app/Views/runner/step.php` — the three-state UI.

## Data used

- **Reads:** the project, recipe + checks, Pantry fields/values, and the
  artifact with its versions and confirmed checks.
- **Writes:** none in `step()` — the write transitions are paste, checks,
  approve, edit, reopen, restore (see related records).

## Dependencies

The Pantry must be saved (`fill-pantry`); the prompt comes from `build-prompt`.

## Dependents

**Paste a response**, **Review Quality Checks**, and **Approve & version**
all act on this step.

## Failure cases

- Not the owner, or unknown project/recipe → 404.
- Pantry not saved → redirect to the Pantry.
- An earlier recipe not yet approved → redirect back to it (strict order).

## Current state

- **Status:** implemented. **Verification:** verified from source — `step()`,
  `load()`, and the state derivation were traced end to end.

## Safe to change

Wizard copy and the review-card presentation are safe to adjust.

## Use caution

The durable state must stay derived from data, never from `?stage=`; that
invariant is what makes a refresh safe. Do not let the wizard parameter gate a
write.

## Why it works this way

Deriving state from data (not the URL) and unlocking steps strictly in order is
what lets later Recipes safely chain earlier approved Artifacts into their
prompts.

## Related functionality

- Build the prompt
- Paste a response
- Review Quality Checks
- Approve & version an artifact
