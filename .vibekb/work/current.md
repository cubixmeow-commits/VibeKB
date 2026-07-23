---
id: current-work
type: work
title: check watches VibeKB's own front-door docs (documentation-claims lint)
objective: Close the gap where `check` validates the .vibekb/ model against source but never verifies the repository's root narrative docs (README, CLAUDE, etc.). Add a deterministic documentation-claims section so dead CLI commands, unresolved internal links, and a stale .vibekb/ structure block in those docs become definite errors instead of silent drift.
summary: Complete. Added a `[5] Documentation claims` section to `php tools/vibekb.php check`. It scans root *.md files for three mechanical drift classes (dead CLI commands, unresolved links, stale README structure block) and fails on definite errors, matching the honesty model of the broken-file-references section. Verified clean on the repo and failing on injected drift.
requested_by: User (follow-up to the README audit: "the tool doesn't watch its own front door")
status: complete
verification_state: verified-manually
updated: 2026-07-23
affected_functionality: [detect-drift]
expected_files: [tools/vibekb.php, .vibekb/functionality/records/detect-drift.md, .vibekb/memory/changes/check-watches-front-door-docs.md, .vibekb/work/handoff.md, .vibekb/work/current.md]
data_impact: None — adds a read-only static check to the CLI. No model data or runtime data written.
risks:
  - A lint that emits false positives is worse than none; each sub-check must degrade to "skipped" when its reference source is absent (e.g. the Go CLI source in a downstream install) rather than falsely flag.
  - Only root-level *.md are scanned; generated /docs and examples/ must stay out of scope.
  - Command-existence checks must derive the valid command set from the actual dispatch (this script + internal/cli/cli.go), never a hardcoded list that can itself drift.
---

## Status

Complete and verified manually. Three checks over root `*.md`:

1. **Command existence** — CLI invocations in fenced code blocks
   (`php …/vibekb.php <sub>` and `vibekb <sub>`) must name a real subcommand.
   PHP subs are parsed from this script's dispatch; Go subs from
   `internal/cli/cli.go` (skipped if that source is absent).
2. **Internal link resolution** — every relative `](target.md)` /
   `](dir/)` link must resolve on disk.
3. **Structure-block parity** — a fenced `.vibekb/` tree must match the real
   top-level of `.vibekb/`.

Definite errors (dead command, dead link, doc claims a dir that is gone) fail
`check`, like broken file references. Softer signals (a real dir the doc omits)
are warnings.
