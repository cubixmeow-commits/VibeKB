---
id: build-prompt
type: functionality
title: Build the prompt
area: cookbook-runner
summary: Turns a Recipe template into a ready-to-copy prompt by substituting Pantry values and earlier approved Artifacts, with every substitution highlighted.
status: implemented
verification: verified-from-source
user_facing: true
trigger: Rendered whenever a Recipe step is shown.
updated: 2026-07-16
tags: [runner, prompt, chaining]
files: [app/Services/PromptBuilder.php, app/Models/PantryField.php, app/Models/Project.php, app/Models/Artifact.php]
reads: [pantry_fields, pantry_values, artifacts, artifact_versions]
writes: []
config: []
depends_on: [fill-pantry]
related_memory: [decision:immutable-artifact-versions]
---

## In one sentence

`{{field_key}}` becomes your Pantry value and `{{artifact:recipe-slug}}` becomes
an earlier approved Artifact — so what you see highlighted is exactly what gets
copied.

## Current behavior

`PromptBuilder::build()` returns `{text, html, missing}`. It walks one
substitution map — Pantry field keys plus `artifact:slug` for every approved
Artifact — replacing placeholders. `text` is the plain prompt to copy; `html`
is the escaped rendering with each substituted value wrapped in a
`.prompt-ingredient` span; `missing` lists any unfilled placeholders (rendered
as `[missing: key]`). Multiselect Pantry values join into a readable list.

## Step-by-step flow

1. The Runner calls `PromptBuilder::build($recipe, $project)`.
2. `substitutions()` gathers Pantry values and approved-Artifact contents.
3. Each `{{...}}` placeholder is replaced in both text and highlighted HTML.
4. Unfilled placeholders are collected into `missing`.
5. The Runner shows the highlighted prompt and a copy control.

## Implementation map

- `app/Services/PromptBuilder.php` — `build()`, `substitutions()`, `presentValue()`.
- `app/Models/Artifact.php` — `approvedByRecipe()` supplies chained content.

## Data used

- **Reads:** Pantry values; approved Artifact versions.
- **Writes:** none.

## Dependencies

A saved Pantry (`fill-pantry`); approved Artifacts from earlier steps.

## Dependents

**Run a Recipe** renders the result; the copied prompt drives the whole loop.

## Failure cases

- A referenced field or artifact that is empty renders `[missing: key]` and is
  reported in `missing` — the prompt is never silently broken.

## Current state

- **Status:** implemented. **Verification:** verified from source
  (`PromptBuilder` traced in full).

## Use caution

Text and HTML walk the same map on purpose — what is highlighted is what is
copied. Do not let them diverge.

## Why it works this way

Chaining earlier approved Artifacts into later prompts is what makes a Cookbook
a coherent multi-step workflow rather than isolated prompts — and it depends on
the immutable, approved version being the one that chains.

## Related functionality

- Run a Recipe
- Approve & version an artifact
