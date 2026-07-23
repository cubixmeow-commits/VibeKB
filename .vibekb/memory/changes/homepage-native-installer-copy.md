---
id: homepage-native-installer-copy
type: change
title: Homepage copy matches the native Go installer
summary: The public homepage now describes the real source-build Go CLI workflow (clone → go build → vibekb install → coding agent), splits install vs post-install requirements, drops Native CLI and Repository doctor from Coming soon, and removes em dashes from homepage marketing copy.
status: implemented
verification: verified-from-source
updated: 2026-07-23
functionality: [install-into-a-repository, initialize-in-a-repository, deploy-and-stay-portable]
files: [index.php, .vibekb/project/identity.md]
tags: [homepage, installer, onboarding, copy, go, change]
---

## Before

The homepage still taught the old PHP installer path:

1. `git clone …`
2. `php VibeKB/install.php /path/to/your/project` with “Requires PHP 8.2+”
3. Coding-agent prompt

Install Requirements and Current Requirements listed PHP as if it were needed to
install. Coming soon still advertised Native CLI and Repository doctor, which
are already implemented. Marketing copy used em dashes heavily.

## After

Page flow is unchanged (hero → install → compatibility → what you get → proof).
Commands match `INSTALLER.md` / `README.md` / `go.mod`:

1. Clone + `go build -o vibekb ./cmd/vibekb` (Go 1.24+)
2. `./vibekb install /path/to/your/project` (native; no PHP required to install)
3. Same integration prompt for the coding agent

Requirements are split: Go/Git/write access to install from source today; PHP
8.2+ and an AI coding agent after installation. Coming soon lists only Phase 2
distribution items (prebuilt binaries, Homebrew, Winget, curl). The identity
one_liner used on the hero card no longer uses em dashes.

## Honesty preserved

Installation is native Go; the guide and model engine remain PHP. `docs/` is
still generated with `php tools/vibekb.php generate`. No brew/winget/curl
commands are shown as available. The installer still does not analyze the target
application.

## Verification note

Commands and rendered homepage HTML were checked against `INSTALLER.md`,
`go.mod`, and `internal/installer` argument parsing. Old `install.php` /
“Requires PHP 8.2+” install-card / Native CLI / Repository doctor strings are
absent from the rendered homepage. Copy-button clicks were not exercised in a
browser in this environment.
