---
id: handoff
type: handoff
title: Current handoff
summary: A Go developer CLI (`vibekb`) now fronts the PHP toolchain as Phase 1 of the "Go front-end, PHP core" architecture. It delegates every model-semantic command and never forks the model loader. Next: distribution (Phase 2) — release binaries and a brew/winget/curl install path.
updated: 2026-07-23
verification_state: verified-from-source
---

## Current state

VibeKB has a new developer front-end: a Go binary (`cmd/vibekb`, `internal/*`)
that runs `doctor`/`version`/`help` natively and delegates `status`, `check`,
`affected`, `bootstrap`, `validate`, `generate`, and `install` to the existing PHP
tooling. Nothing in PHP changed — the model loader, the dynamic guide (Mode A),
and the static generator (Mode B) are untouched, and `php tools/vibekb.php …`
still works exactly as before.

## Completed this change

- **Assessment** (`ARCHITECTURE.md`): responsibilities inventoried; the model
  loader identified as the crux; three architectures weighed; "Go front-end, PHP
  core" chosen with a command-by-command verdict and a 4-phase roadmap.
- **Phase 1 CLI**: native `doctor`/`version`/`help`; repo-root + PHP discovery
  (`internal/phpcore`); delegation with exit-code propagation; honest
  missing-runtime and outside-a-source-clone errors; a unit test for the PHP
  version floor.
- **CI**: a Go job (build + vet + test) added alongside the PHP job.
- **Model reconciled**: new functionality `run-the-developer-cli` under a new
  `developer-cli` area; `decision:go-front-end-php-core`; provenance scope,
  current-work, and handoff updated; `/docs` regenerated.
- **Kept the runtime pure**: Go excluded from the cPanel deploy (`.cpanel.yml`)
  and from the installer payload (`template/manifest.json` → `not_installed`);
  `.gitignore` added for the Go binary and build cache.

## Verification completed

- `go build ./...`, `go vet ./...`, `go test ./...` — all pass; `gofmt` clean.
- Built the binary and ran it against this repo: `version`, `doctor`, delegated
  `validate`/`status`/`check` (from a nested directory), unknown-command exit 2,
  missing-PHP exit 1, `install` outside a clone reported.
- `php tools/vibekb.php validate` — 0 errors; `check --strict`;
  `test-topology.php`; `/docs` regenerated.

## Not done yet (roadmap)

- **Phase 2 — distribution**: cross-compiled release binaries + checksums, a
  `curl | sh` installer, a Homebrew tap, and a winget manifest.
- **Phase 3 — shared core boundary**: lift the model core out of `guide/lib` into
  a shared location both the guide and tools import; turn the `Starter.php` starter
  payload into data files.
- **Phase 4 — evaluate a bundled PHP runtime** (FrankenPHP / static-php-cli) so
  `vibekb` needs nothing preinstalled. A later decision with real trade-offs.

## Exact next recommended action

`php tools/vibekb.php status` (or `vibekb status`) before the next change. To
continue the roadmap, start Phase 2: add release automation and a `curl | sh`
installer that fetches the right `vibekb` binary. Keep the honesty boundary — the
CLI must never grow a second model loader or overstate that PHP is unnecessary.
