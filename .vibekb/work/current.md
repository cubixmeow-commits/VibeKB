---
id: current-work
type: work
title: Verify CSV export escaping
summary: Confirm that exported CSV correctly escapes idea notes containing commas, quotes, and newlines, then promote export from experimental to implemented.
objective: Make the CSV export trustworthy for real notes and update its status honestly.
requested_by: Project owner
status: in-progress
verification_state: not-verified
updated: 2026-07-20
affected_functionality: [export-ideas]
expected_files: [public/export.php, tests/ExportTest.php]
data_impact: None — export is read-only.
risks: [Low. Export does not write data; worst case is a malformed download.]
---

## What the user asked for

"Make sure the export doesn't break when my notes have commas or line breaks —
right now I don't trust it."

## What the software currently does

`public/export.php` streams all ideas as CSV using `fputcsv()`. `fputcsv()`
should quote fields containing separators, but this has never been verified
against real multi-line notes, so `export-ideas` is marked **experimental /
needs-verification**.

## What it should do after this work

Export a valid CSV for any note content, confirmed by a test that includes
commas, double quotes, and newlines. On success, `export-ideas` moves to
**implemented / verified-by-test**.

## Affected functionality

- `export-ideas` (the target).
- `browse-ideas` (shares the `all()` query — must not regress).

## Files expected to change

- `public/export.php` — only if a defect is found.
- `tests/ExportTest.php` — new test covering awkward note content.

## Data that could change

None. Export is read-only.

## Dependencies that matter

Relies on `initialize-database` and the `all()` query in `IdeaRepository`.

## Risks

Low. No writes are involved.

## Completed so far

- Reproduced the concern: identified that no test covers special characters.

## Remaining

- Add the test with commas/quotes/newlines.
- Fix escaping if the test fails.
- Update the `export-ideas` record's status and verification.

## How the result will be verified

Open the exported file in a spreadsheet and confirm rows and columns line up
for a note containing `a, b`, `"quoted"`, and a line break.

## Repository memory to add

If a defect is found, add a `discovery` record; update the `export-ideas`
record either way.
