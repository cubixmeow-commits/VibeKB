---
id: check-watches-front-door-docs
type: change
title: check gains a documentation-claims lint for the front-door docs
summary: `php tools/vibekb.php check` now has a fifth section that lints the repository's root narrative docs (README.md, CLAUDE.md, …) for three mechanical drift classes — dead CLI commands, unresolved internal links, and a stale README `.vibekb/` structure block — turning front-door drift into a definite error instead of a silent gap.
status: implemented
verification: verified-manually
updated: 2026-07-23
functionality: [detect-drift]
files: [tools/vibekb.php]
tags: [cli, drift, documentation, self-maintenance, change]
---

## Before

`check` validated the `.vibekb/` model against source (validation, broken file
references, source drift, `/docs` sync) but nothing watched the root-level
narrative docs. A README audit found that the front door had drifted in the past
(a superseded architecture, commands that had been renamed) with no mechanism in
the repository that would have caught it. The drift-detection tool did not watch
its own front door.

## After

A new `vibekb_check_docs_claims()` runs as section **[5] Documentation claims**.
Over every root-level `*.md` it performs three deterministic, token-free checks:

- **Command existence.** Every `php …/vibekb.php <sub>` and `vibekb <sub>`
  invocation inside a fenced code block must name a real subcommand. The valid
  sets are parsed from the actual dispatch — this script's `switch`, and
  `internal/cli/cli.go` (native + delegated) — so the lint cannot disagree with
  the code it checks.
- **Internal links.** Every relative Markdown link target must resolve on disk.
- **Structure parity.** README's `.vibekb/` tree block must match the real
  top-level of `.vibekb/` (README only — INSTALLER's tree describes a *target*
  install and SCHEMA's the schema, so neither is compared to this repo).

Definite contradictions (dead command, dead link, a block naming a directory
that is gone) fail `check` like a broken file reference. A real directory the
block merely omits is a warning. When a check's source of truth is absent — e.g.
`internal/cli/cli.go` in a downstream install — that sub-check prints a "skipped"
note rather than emitting a false error.

## Verification

Manually exercised on 2026-07-23: clean pass on the real repo (14 root docs);
negative tests confirmed each check fails on injected drift (a bogus
`vibekb.php` subcommand, a bogus `vibekb` command, a broken link, and a bogus
`.vibekb/` structure entry); and the Go-source-absent path confirmed to skip
rather than false-flag. The first run also caught and drove the fix of a real
`ltrim($t, './')` bug that had mangled `./.github/...` link targets — a reminder
that the lint's own correctness matters as much as its coverage.

## Honesty preserved

The check stays strictly on the *detected* side of VibeKB's detection/interpretation
boundary: it proves mechanical contradictions in the docs but never judges whether
prose is accurate in meaning. It does not rewrite docs or claim to. `updates_automatically`
is unchanged.
