---
id: run-the-developer-cli
type: functionality
title: Run VibeKB from one developer CLI
area: developer-cli
summary: A single Go binary (`vibekb`) that is the developer's front door to VibeKB — downloadable from GitHub Releases, installs VibeKB natively (embedded payload, no PHP), runs environment diagnostics natively, and delegates every model-semantic command to the canonical PHP tooling it discovers, so there is exactly one model loader.
status: implemented
verification: verified-from-source
user_facing: true
trigger: A developer runs `vibekb <command>` (e.g. `vibekb install`, `vibekb doctor`, `vibekb check`) — install from anywhere, model commands from inside a VibeKB repository.
updated: 2026-07-23
tags: [cli, go, developer-tool, delegation, native-install, distribution]
files: [cmd/vibekb/main.go, internal/cli/cli.go, internal/cli/doctor.go, internal/cli/version.go, internal/phpcore/phpcore.go, internal/buildinfo/buildinfo.go, go.mod, .github/workflows/release.yml, RELEASE.md]
reads: [tools/vibekb.php]
writes: []
config: []
depends_on: [install-into-a-repository, bootstrap-workspace, validate-model, detect-drift, generate-static-snapshot]
related_memory: [decision:go-front-end-php-core, decision:native-installer-embedded-payload, decision:two-modes-one-source, change:release-binaries-pipeline]
---

## In one sentence

`vibekb` gives developers one portable command for VibeKB: native diagnostics and
help that need no runtime, and a thin, honest hand-off to the existing PHP core
for everything that touches the model — never a second implementation of it.

## User experience

Primary distribution is a downloadable binary from GitHub Releases (no Go
required). `vibekb version` prints identity stamped at link time:

```
VibeKB
Version: 0.1.0
Commit: 84c81d2
Built: 2026-07-22
Platform: darwin/arm64
```

plus detected PHP and repository when present. Release builds inject Version,
Commit, and Built via ldflags (see `.github/workflows/release.yml` and
`RELEASE.md`). Development builds keep `0.1.0-dev` / `unknown` / `dev`.

A developer runs `vibekb` from anywhere inside a VibeKB repository. `vibekb doctor`
reports whether PHP 8.2+, git, and a `.vibekb/` workspace are present and ends with
a clear OK or "attention needed". `vibekb check`, `status`, `generate`, and the rest
behave exactly like `php tools/vibekb.php <command>` — same output, same exit
codes — because the binary runs that very script. If PHP is missing, the command
fails with a plain message telling the developer to install PHP 8.2+ or set
`VIBEKB_PHP`, and to run `vibekb doctor`.

## Current behavior

`cmd/vibekb` dispatches through `internal/cli`. `help`, `version`, `doctor`, and
`install` are native Go — `install` runs entirely from the binary's embedded
payload (see **Install VibeKB into a repository**) and launches no PHP. `status`,
`check`, `affected`, `bootstrap`, `validate`, and `generate` are delegated to
`tools/vibekb.php`. `internal/phpcore` performs discovery for the delegated
commands: it walks up from the working directory to find the repository root (a
directory holding `tools/vibekb.php` or `.vibekb/`) and resolves the PHP
executable from `VIBEKB_PHP` or the usual names on `PATH`. Delegation runs `php
<script> <subcommand> <args…>` with the repository as the working directory and
stdio wired straight through, and returns the child process's exit code.

## Honesty boundary

The CLI never re-implements the VibeKB content model. Parsing, validation, drift
interpretation, and HTML generation have exactly one implementation — the PHP core
(`guide/lib`, `tools/`). The Go layer only orchestrates it and adds diagnostics
that are useful *before* PHP is even present. It also never hides the PHP
dependency: `doctor` states it plainly rather than pretending it is not there.

## Step-by-step flow

1. `vibekb <command>` → `internal/cli` dispatch.
2. Native command (`doctor`/`version`/`help`) → run in Go and return.
3. Delegated command → `phpcore.Discover()` finds the repo root and PHP.
4. If the repo or PHP is missing, print a specific, actionable error and exit
   non-zero.
5. Otherwise run the PHP script with the subcommand and pass through its exit code.

## Implementation map

- `cmd/vibekb/main.go` — entry point; hands `os.Args` to the CLI.
- `internal/cli/cli.go` — command dispatch, help, and the delegation map.
- `internal/cli/doctor.go` — native environment check (PHP ≥ 8.2, git, workspace).
- `internal/cli/version.go` — version and detected-runtime output.
- `internal/phpcore/phpcore.go` — repo/source/PHP discovery and delegation.
- `internal/buildinfo/buildinfo.go` — Version, Commit, Built (set at link time on release).
- `.github/workflows/release.yml` — tag-triggered cross-platform release builds.
- `RELEASE.md` — how to publish a version.

## Data used

- **Reads:** the PHP script it delegates to (`tools/vibekb.php`) and the
  surrounding directory tree (to locate the repository and PHP); for `install`, its
  own embedded payload. It reads no `.vibekb/` content itself — the delegated PHP
  does.
- **Writes:** nothing directly; any writes are performed by the delegated PHP
  (e.g. `bootstrap` scaffolding, `generate` writing `/docs`).

## Dependencies

Delegates to the PHP commands documented as their own functionality:
`install-into-a-repository`, `bootstrap-workspace`, `validate-model`,
`detect-drift`, and `generate-static-snapshot`.

## Failure cases

- Not inside a VibeKB repository → reported; exit non-zero (delegated commands).
- `install` into VibeKB's own self-hosted repo → refused; into a non-project dir
  → confirmation prompt.
- PHP absent → reported with how to install it or set `VIBEKB_PHP`; exit non-zero.
- A delegated command failing → its exit code is propagated unchanged.

## Safe to change

Adding a native command, or mapping a new subcommand to a PHP script in the
delegation table, extends the CLI without touching the model core. Help text and
`doctor` output are presentation.

## Use caution

The CLI must stay a front-end: it must not grow a second implementation of model
parsing, validation, or HTML generation, and it must not overstate what works
without PHP. Keep every model-semantic operation delegated to the one PHP core.

## Why it works this way

VibeKB's value is resisting drift and honest provenance — "the model is the API".
A second model loader (one in Go, one in PHP) is the most dangerous thing that
could be added, because a disagreement between them fails silently. Fronting the
one PHP core with a Go binary delivers a single professional command and a real
installation path while keeping exactly one implementation of the model. See
`ARCHITECTURE.md` for the full assessment and roadmap.
