---
id: docs-is-generated-never-hand-edit
type: warning
title: /docs is generated output — never hand-edit it
summary: The static site under `/docs` is produced by `tools/generate-static.php`; editing it directly is lost on the next build and makes the snapshot lie about the model.
severity: medium
status: active
verification: verified-from-source
updated: 2026-07-22
functionality: [generate-static-snapshot, detect-drift]
files: [tools/generate-static.php, docs/index.html]
tags: [gotcha, generated-output]
---

## What can go wrong

Someone fixes a typo or tweaks wording directly in a `/docs/*.html` file. The
next `generate-static.php` run overwrites it, and until then the published site
shows content that does not exist in `.vibekb/` — the snapshot no longer reflects
the model.

## Cause

`/docs` is a projection, not a source. The generator cleans and rewrites the
generated site on every run.

## What not to do

Do not edit files under `/docs`. Change `.vibekb/` (and, if needed, the templates
in `guide/`) and regenerate.

## Detection

`php tools/vibekb.php check` regenerates into a temporary directory and compares
against `/docs` (ignoring the volatile generation timestamp); a difference means
`/docs` is stale or was hand-edited. Under `--strict` this fails.

## Safe procedure

1. Edit `.vibekb/` (or the shared templates).
2. Run `php tools/validate.php`.
3. Run `php tools/generate-static.php`.
4. Commit the regenerated `/docs` alongside the model change.
