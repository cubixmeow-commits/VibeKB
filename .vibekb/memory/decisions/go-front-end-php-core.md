---
id: go-front-end-php-core
type: decision
title: A Go developer CLI fronts VibeKB; PHP stays the single model core and runtime
summary: VibeKB adds a Go binary (`vibekb`) as a portable developer front-end that delegates every model-semantic operation to the canonical PHP core, rather than re-implementing the model loader in Go. PHP remains the one implementation of parsing, validation, and generation, and the only deployment runtime.
status: accepted
verification: verified-from-source
updated: 2026-07-23
functionality: [run-the-developer-cli, load-living-model, generate-static-snapshot]
files: [cmd/vibekb/main.go, internal/cli/cli.go, internal/phpcore/phpcore.go, ARCHITECTURE.md]
tags: [architecture, cli, go, php, distribution, anti-drift]
---

## Context

VibeKB is evolving toward a developer tool whose command line is the primary
interface (like git, cargo, or kubectl). Go is attractive for that: a single
static binary, native filesystem/git work, and a `brew`/`winget`/`curl` install
story with no runtime to preinstall. The question was whether to port VibeKB's
commands (`install`, `bootstrap`, `check`, `generate`) to Go.

The decisive constraint is that those commands are thin. Their real behaviour
lives in the model loader (`guide/lib/Content.php`, ~1,240 lines) that both the
CLI tools and the dynamic guide already share. Porting the commands means porting
the loader — creating a second implementation of the thing VibeKB exists to keep
honest.

## Decision

Introduce a Go CLI (`cmd/vibekb`, `internal/*`) as a **front-end only**:

- Native Go for what must work without the model core: `doctor` (environment
  diagnostics), `version`, `help`, repo/PHP discovery, and honest missing-runtime
  errors.
- Delegate every model-semantic command (`status`, `check`, `affected`,
  `bootstrap`, `validate`, `generate`, `install`) to the existing PHP tooling,
  which the binary discovers and runs.

PHP remains the single source of truth for the model loader, the guide runtime
(Mode A), and static generation (Mode B — the same templates, no second template
system). `php tools/vibekb.php …` keeps working unchanged.

## Alternatives considered

- **Full Go port; PHP as runtime only** — rejected: it forks the model loader
  into two languages. The day the Go and PHP validators disagree, VibeKB's
  anti-drift promise breaks silently. This is the "do not replace PHP simply
  because Go exists" case.
- **Status quo plus packaging** — rejected as insufficient: it leaves the real DX
  gap (PHP required just to try the tooling; no single-binary install path).
- **Generate HTML from Go** — rejected specifically: Mode B exists so there is no
  second template system; a Go generator would reintroduce that duplication.

## Reason

The valuable, drift-sensitive logic must have exactly one implementation. A Go
front-end delivers the distribution and UX wins without touching that
implementation, and it is purely additive — nothing that works today changes.
Making PHP progressively invisible (distribution, and later an optional bundled
runtime) is staged in ARCHITECTURE.md, not assumed.
