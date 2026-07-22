---
id: fill-pantry
type: functionality
title: Fill the Pantry
area: pantry-inputs
summary: Collects and validates the project facts every prompt is built from, with per-type validation, a one-click sample fill, and a value-driven project rename.
status: implemented
verification: verified-from-source
user_facing: true
trigger: A user opens or submits "/projects/{id}/pantry".
updated: 2026-07-16
tags: [pantry, forms, validation, write]
files: [app/Controllers/ProjectController.php, app/Views/projects/pantry.php, app/Models/PantryField.php, app/Models/Project.php]
reads: [pantry_fields, pantry_values, projects]
writes: [pantry_values, projects]
config: []
depends_on: [start-project]
related_memory: [assumption:single-writer-per-project, warning:read-write-path-coupling]
---

## In one sentence

Fill in the Cookbook's ingredients once; those values feed every Recipe's
prompt for the whole project.

## User experience

A form of typed fields (text, textarea, select, multiselect, number, url) with
help text. "Fill with a sample Pantry" pre-fills the seeded sample values
(nothing saved until you submit), clearly marked as sample.

## Current behavior

`pantry()` renders the form with any stored values (decoding multiselect JSON);
`?sample=1` pre-fills seeded samples without saving. `savePantry()` validates
each field by type (length caps, option membership, numeric range, URL scheme),
stores values (multiselect as JSON), marks `pantry_saved_at`, and — if the
Cookbook has a `product_name` field — renames the project to that value. First
save routes to Recipe 1; later saves route to the next unfinished Recipe.

## Step-by-step flow

1. User opens `/projects/{id}/pantry` (or clicks "Fill with a sample Pantry").
2. `savePantry()` validates each field by type (422 + re-render on error).
3. Valid values are stored in `pantry_values` (multiselect as JSON).
4. `pantry_saved_at` is set; a `product_name` value renames the project.
5. First save → Recipe 1; later save → next unfinished Recipe.

## Implementation map

- `app/Controllers/ProjectController.php` — `pantry()`, `savePantry()`.
- `app/Models/PantryField.php` — field definitions and `options()`.
- `app/Models/Project.php` — `savePantry`, `markPantrySaved`, `pantryValues`.

## Data used

- **Inputs:** one value per Pantry field.
- **Writes:** `pantry_values` (unique per project+field); `projects` title +
  `pantry_saved_at`.

## Dependencies

A project (**Start a project**). Consumed by **Build the prompt**.

## Failure cases

- Required field empty, over length, bad option, out-of-range number, or
  non-https URL → 422 with per-field messages; nothing saved.

## Current state

- **Status:** implemented. **Verification:** verified from source
  (`ProjectController::pantry/savePantry` traced).

## Use caution

The Pantry is read by **Build the prompt** and rendered into the export
manifest. Changing a field key or type without updating those read paths breaks
prompts silently — see the `read-write-path-coupling` warning.

## Why it works this way

The Runner refuses to start until the Pantry is saved, because every prompt is
built from these facts.

## Related functionality

- Build the prompt
- Run a Recipe
