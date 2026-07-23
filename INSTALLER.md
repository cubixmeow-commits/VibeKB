# INSTALLER.md — Installing, upgrading, and repairing VibeKB

VibeKB ships with a first-class installer so adopting it into a repository is a
command, not a manual copy. This document explains the installer end to end:
installation, upgrades, repairing a damaged workspace, the template structure,
and where to extend it.

> **The one thing to remember:** the installer *prepares the workspace*; an AI
> coding agent *interprets the software*. The installer never analyses,
> understands, or documents your application — it lays down an empty, valid
> VibeKB workspace and hands off to your agent. That separation is deliberate and
> is what keeps VibeKB honest.

## Quick start

```bash
git clone https://github.com/cubixmeow-commits/VibeKB.git
php VibeKB/install.php /path/to/your/project
```

or, from inside the VibeKB clone, install into the current directory:

```bash
php install.php
```

Then open your project in a coding agent (Claude Code, Cursor, Codex, …) and ask
it to **build the first VibeKB model using `prompts/INTEGRATE_VIBEKB.md`**.

Requirements: **PHP 8.2+**. No Composer, no framework, no network, no build step.
Works on Windows, macOS, and Linux.

> **`vibekb install` (Go front-end).** If you have the `vibekb` binary built (see
> the README and `ARCHITECTURE.md`), `vibekb install /path/to/your/project` does
> exactly the same thing — it runs this same `install.php` from your source clone.
> The Go CLI is a portable front door; the installer logic below is unchanged and
> remains the canonical implementation. Everything in this document applies to
> both entry points.

## What gets installed

The installer copies the **VibeKB-owned runtime** into your repository and creates
a fresh, empty **`.vibekb/` workspace**. Three responsibilities stay cleanly
separated:

| Where | What | Who owns it |
|-------|------|-------------|
| `guide/`, `tools/`, `prompts/`, `.cursor/`, VibeKB docs | The **installed runtime** — the app, the CLI, the agent instructions | VibeKB (safe to refresh on upgrade) |
| `.vibekb/` | The **living software model** — your project's knowledge | Your project (never overwritten) |
| `docs/` | The **generated static snapshot** (`php tools/vibekb.php generate`) | Generated output (not installed) |

The exact payload is declared in [`template/manifest.json`](./template/manifest.json),
so "what belongs in another repository" is explicit and versioned — not hidden in
the installer's code.

### What is deliberately *not* installed

The marketing homepage (`index.php` and the root `assets/`) is VibeKB's own
landing page and would collide with your application's root, so it is not
installed — the product you use in a target repository is the **guide** at
`guide/`. Also excluded: VibeKB's own `.vibekb/` model, `examples/`, generated
`docs/`, and host-specific deploy config (`.cpanel.yml`, `DEPLOYMENT.md`).

## The installation flow

Running `php install.php [target]` does the following:

1. **Resolve source and target.** The installer's own directory is the VibeKB
   source; the argument (or the current directory) is the target. It refuses to
   install into VibeKB's own repository (which is self-hosted).
2. **Detect the repository.** It confirms the target looks like a software project
   (a `.git` directory, common source folders, a README, or a manifest like
   `package.json`/`composer.json`/`go.mod`). If not, it asks for confirmation.
3. **Show the plan.** Before changing anything it prints exactly what will be
   **created**, **replaced**, **skipped**, and whether `.vibekb/` will be a fresh
   model or preserved.
4. **Copy the runtime.** It creates only missing directories and, on a fresh
   install, never overwrites a pre-existing file (those are skipped and reported)
   unless you pass `--force`. Your application's code is never touched.
5. **Scaffold a fresh model.** It writes an empty-but-valid `.vibekb/` (see
   *Template structure* below) and records installer state in
   `.vibekb/.installer.json`.
6. **Verify.** It checks the guide, tools, prompts, and starter model are present,
   and runs the freshly installed `php tools/vibekb.php check` against the target
   (an empty model is valid and reports OK).
7. **Hand off.** It prints the next action: build the model with
   `prompts/INTEGRATE_VIBEKB.md`.

### Options

| Option | Effect |
|--------|--------|
| `--dry-run` | Show the full plan (every file) and change nothing. |
| `--yes`, `-y` | Assume "yes" to prompts — non-interactive install. |
| `--force` | Overwrite pre-existing files, **including** resetting an existing `.vibekb/` model. Never silent. |
| `--upgrade` | Refresh the runtime and preserve `.vibekb/` (auto-detected when a prior install exists). |
| `--help`, `-h` | Usage. |

### Dry run

```bash
php install.php --dry-run /path/to/your/project
```

prints the create/replace/skip plan and the full per-file list, and writes
nothing. Use it to see exactly what an install or upgrade would do.

## Upgrading

When the installer finds a prior installation (a `.vibekb/.installer.json`
marker), it switches to **upgrade mode** automatically:

```bash
php VibeKB/install.php /path/to/your/project     # auto-upgrade
# or force it explicitly:
php VibeKB/install.php --upgrade /path/to/your/project
```

An upgrade **refreshes the VibeKB-owned payload** (`guide/`, `tools/`, `prompts/`,
`.cursor/`, docs) and **preserves your `.vibekb/` model** untouched. It also
repairs any missing workspace scaffolding without overwriting your content. To
reset the model as well, pass `--force` (this is the only way an upgrade touches
`.vibekb/`).

The installer records the template version in `.vibekb/.installer.json` and shows
the version transition (e.g. `1.0.0 → 1.1.0`) at the top of the run.

## Repairing a workspace — `bootstrap`

`bootstrap` is "git init for VibeKB": it makes sure a valid, empty workspace
exists, creating any missing directories and starter files and **never
overwriting existing content**.

```bash
php tools/vibekb.php bootstrap            # verify and repair
php tools/vibekb.php bootstrap --dry-run  # report only
```

Use it to recover a partially deleted or hand-damaged `.vibekb/`, or to create a
workspace in a repository that already has the VibeKB runtime. Like the
installer, it **never** generates functionality, invents diagrams, inspects your
source, or writes documentation about your software.

The installer and `bootstrap` share one definition of the starter workspace
(`tools/lib/Starter.php`), so they can never disagree about what a fresh model
contains.

## Template structure

The "template" is intentionally **not** a duplicated copy of the runtime (that
would drift instantly in a self-hosted repository). It is a small, declarative
definition plus a generator:

```
template/
    manifest.json     # declares the installable payload, preserved paths,
                      # generated paths, and deliberately-excluded paths
    README.md         # orientation for the template system
tools/lib/Starter.php # the single source of truth for the fresh .vibekb/
                      # workspace (dirs + starter files), shared by the
                      # installer and `bootstrap`
```

A freshly scaffolded `.vibekb/` contains:

```
.vibekb/
    manifest.json                 # provenance intentionally blank until built
    project/                      # identity, intent, current-state, constraints (placeholders)
    functionality/index.json      # empty groups + order (no invented functionality)
    functionality/records/        # empty — the agent adds records
    system/                       # mental-model, components, flows, storage, deployment (placeholders)
    files/important-files.json    # empty
    diagrams/                     # index.json (empty) + records/ + assets/ + topology/
    memory/                       # decisions, constraints, assumptions, warnings, discoveries, changes
    work/current.md, work/handoff.md   # point at the integration prompt
    .htaccess                     # deny direct web access to the model files
    .installer.json               # installer state (version + owned payload)
```

Every starter record is an explicit **placeholder** that tells the agent what to
write; none claims your software does anything. The provenance in
`manifest.json` is left blank on purpose — no commit was analysed, so none is
recorded. The empty model is valid and passes `php tools/vibekb.php check`.

## Future extension points

- **Add to the payload.** To install another VibeKB-owned file or directory, add
  its repository-relative path to the appropriate list in
  `template/manifest.json` (`runtime`, `agent`, or `docs`). The installer picks it
  up with no code change; dry-run to confirm.
- **Change the starter workspace.** Edit `vibekb_starter_dirs()` and
  `vibekb_starter_files()` in `tools/lib/Starter.php`. Both the installer and
  `bootstrap` update together. Keep every starter file valid so a fresh workspace
  still passes `check`.
- **Version the template.** Bump `template_version` in `template/manifest.json`
  when the payload or starter changes. The installer records it in
  `.vibekb/.installer.json` and reports the transition on upgrade.
- **Exclude new VibeKB-repo-only paths.** Distribution/tooling paths that must
  never install into a target belong in the `not_installed` list (documentation)
  and in the drift-exclusion set in `tools/vibekb.php`.

## Manual installation (advanced, appendix)

The installer is the supported path. If you must install by hand — for example
into an environment where you cannot run the installer — copy the payload
declared in `template/manifest.json` into your repository (`guide/`, `tools/`,
`prompts/`, `.cursor/`, and the VibeKB docs), then create the workspace with:

```bash
php tools/vibekb.php bootstrap
```

That produces the same fresh, valid `.vibekb/` the installer would. Then follow
[`INITIALIZE.md`](./INITIALIZE.md) to build the model.

## Troubleshooting

- **"The target is the VibeKB source repository itself."** You ran the installer
  against VibeKB's own repo. Install into a *different* repository, or use
  `php tools/vibekb.php bootstrap` to verify/repair this repo's workspace.
- **"existing file(s) were SKIPPED for safety."** A fresh install found files that
  already exist at payload paths. Review them; re-run with `--force` to replace,
  or remove/rename them first. Application code is never replaced without
  `--force`.
- **`check` reports provenance warnings after install.** Expected — the model is
  empty until an agent builds it and records the analysed commit. The install is
  still valid.
