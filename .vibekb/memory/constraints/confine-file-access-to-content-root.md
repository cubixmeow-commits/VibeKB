---
id: confine-file-access-to-content-root
type: constraint
title: All content file access is confined to the content root
summary: The loader refuses any path that escapes the configured `.vibekb/` root, and record ids are constrained to a safe character set, so a crafted request can never read arbitrary files.
status: active
verification: verified-from-source
updated: 2026-07-22
functionality: [load-living-model, render-guide]
files: [guide/lib/Content.php, guide/index.php]
tags: [security, constraint]
---

## The constraint

Every filesystem read the loader performs is checked by `isInsideRoot()`; any
path containing `../` or a null byte, or that does not sit under the content
root, is refused. Record ids in URLs are sanitised to a safe character set before
they are used to look up a record, and diagram SVG/topology filenames are
`basename`-d and pattern-checked.

## Why it matters

The guide serves user-controllable `?view=` and `?id=` values and reads files
from disk. Without confinement, a crafted `?id=../../etc/passwd`-style value could
escape the content directory.

## What not to do

Do not add a file read that trusts a caller-supplied path, and do not relax the
id sanitisation or the `VIBEKB_CONTENT_ROOT` check (which requires the override to
end in `.vibekb`).

## Verification

Traced from source: `isInsideRoot()`, the `?id` sanitisation in `guide/index.php`,
and the SVG/topology filename checks in `Content.php`.
