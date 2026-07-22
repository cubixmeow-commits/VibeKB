---
id: read-write-path-coupling
type: warning
title: Pantry and Artifact read/write paths must change together
summary: A field key, type, or schema change must update the Pantry save, the prompt substitution, the Runner, and the export together, or prompts and kits silently lose data.
severity: high
status: active
verification: verified-from-source
updated: 2026-07-16
functionality: [fill-pantry, build-prompt, run-recipe, export-project-kit]
files: [app/Models/PantryField.php, app/Services/PromptBuilder.php, app/Services/ProjectKit.php]
tags: [data-integrity, gotcha]
---

## Affected functionality

Pantry saves, prompt building, the Runner, and export.

## What can go wrong

A Pantry field key is renamed in the form/schema but not in the prompt template,
so `PromptBuilder` renders `[missing: key]`. Or a value stored for one type is
read as another (multiselect JSON read as plain text). The prompt or export
silently loses the value.

## Cause

The same field flows through several places: `pantry_fields` definition,
`savePantry` validation/storage, `PromptBuilder` substitution, the Runner's
"ingredients used" panel, and `ProjectKit`'s manifest. Changing one without the
others breaks alignment.

## What not to do

Do not rename a field key, change its type, or alter storage encoding in only
one place.

## Safe procedure

1. Update the field definition/seed and both schema dialects.
2. Update `savePantry` validation and storage.
3. Update the `{{field_key}}` in the Recipe prompt template(s).
4. Update `PromptBuilder`/`ProjectKit` presentation if the type changed.
5. Run a project end to end: pantry → prompt → paste → approve → export.

## Verification steps

Confirm the value appears in the prompt (highlighted), the ingredients panel,
and the export manifest.
