# REPOSITORY_SAFETY.md — How VibeKB treats your repository

> **VibeKB keeps its knowledge in `.vibekb/`. It never replaces your existing
> repository instructions. Optional integrations use namespaced files or clearly
> marked managed sections that can be updated or removed safely.**

You should never be afraid to run `vibekb install .` in an existing project. This
document is the guarantee behind that promise: the ownership model, exactly what
VibeKB writes, and how installation, migration, and uninstall stay safe. For the
inventory of the *old* behavior this replaced, see
[REPOSITORY_FOOTPRINT_AUDIT.md](./REPOSITORY_FOOTPRINT_AUDIT.md).

## The one rule

**VibeKB owns `.vibekb/`. Everything outside it is an optional adapter or
integration point.** VibeKB never owns a generic repository file (`README`,
`AGENTS.md`, `CLAUDE.md`, `.gitignore`, your source, your `docs/`).

## Ownership model

VibeKB classifies every path into exactly one of three categories.

### A. VibeKB-owned (safe to create, refresh, and remove)

Everything VibeKB manages lives under `.vibekb/`, plus **namespaced adapter
files** — files VibeKB owns that sit inside a shared tool directory but under a
`vibekb`-specific name, so they can never collide with the tool's own files:

```
.vibekb/
├── manifest.json          # the model manifest
├── install.json           # the installation manifest (ownership, hashes, provenance)
├── project/ functionality/ system/ work/ files/ diagrams/ memory/   # the model
├── runtime/
│   ├── guide/             # the PHP guide (Mode A)
│   ├── tools/             # the PHP self-maintenance CLI
│   └── template/starter/  # starter definition (for bootstrap/repair)
├── reference/             # WORKFLOW.md, PRODUCT.md, SCHEMA.md, MAINTENANCE.md, …
├── prompts/               # INTEGRATE_VIBEKB.md
├── generated/             # optional static snapshot (`vibekb generate`)
└── backups/               # pre-edit backups of shared files

.cursor/rules/vibekb.mdc                       # namespaced adapter (Cursor)
.github/instructions/vibekb.instructions.md    # namespaced adapter (Copilot)
```

VibeKB may create, replace (on upgrade), and delete these freely — they are its
namespace. A namespaced adapter is only ever written to its exact `vibekb`-named
path; sibling files in `.cursor/` or `.github/` are never touched.

### B. Shared (integrate, never own)

Established files that VibeKB may integrate with but must never own — most
importantly `AGENTS.md` and `CLAUDE.md`, the canonical agent-instruction files.
For these, VibeKB:

- never replaces the whole file and never discards existing content;
- edits only inside a single **managed block** (see below);
- preserves everything outside the block byte-for-byte, including line-ending
  style and trailing-newline behavior;
- is idempotent (re-running install makes no change) and never appends a
  duplicate block;
- backs the file up under `.vibekb/backups/` before its first edit;
- detects malformed or duplicated markers and refuses to guess — it reports the
  conflict and changes nothing.

### C. Repository-owned (never modified automatically)

Your source, build config, `README`, `.gitignore`, your `docs/`, and everything
else. VibeKB does not touch these. Where a manual integration would help, VibeKB
reports a recommendation rather than editing the file.

## Managed blocks

When VibeKB integrates with a shared file it inserts exactly one block:

```
<!-- VIBEKB:START v1 -->
…VibeKB-managed pointer into .vibekb/…
<!-- VIBEKB:END -->
```

- The version (`v1`) lets a future format change be detected and re-rendered.
- Only the content **between** the markers is ever rewritten.
- Removing the block (via `vibekb uninstall`) leaves the rest of the file intact.
- If the markers are malformed or duplicated, VibeKB treats it as a conflict:
  it makes no change and tells you what to fix.

## Default installation behavior

`vibekb install .` favors safety over maximum integration:

- **Always:** installs the `.vibekb/` system (runtime, reference, prompts, and a
  fresh empty model). Nothing is written at the repository root.
- **Namespaced adapters** (`.cursor/rules/vibekb.mdc`,
  `.github/instructions/vibekb.instructions.md`) are added **only if that tool is
  already in use** (its directory exists).
- **`AGENTS.md` / `CLAUDE.md`** receive a managed block **only if the file already
  exists**. VibeKB never creates them for you by default.
- An existing `.vibekb/` that VibeKB did **not** create is treated as a collision:
  the install stops and reports it (override only with `--force`).

### Modes and controls

| Command | Behavior |
|---------|----------|
| `vibekb install .` | Safe default above. |
| `vibekb install . --knowledge-only` | Install only `.vibekb/`; touch no integration files. |
| `vibekb install . --no-integrations` | Alias for `--knowledge-only`. |
| `vibekb install . --integrate cursor,claude,agents` | Install only the named adapters, creating them even if the tool is not detected. |
| `vibekb install . --dry-run` | Show every proposed change; write nothing (previews managed-block inserts/updates/conflicts too). |
| `vibekb install . --force` | Narrowly scoped: permits taking over an *unrecognized* `.vibekb/` and resetting the model. It never overwrites shared files wholesale and never touches anything outside `.vibekb/` and the declared adapters. |
| `vibekb install . --upgrade` | Refresh VibeKB-owned files, preserve the model (auto-detected when a prior install exists). |

## Installation manifest & provenance

`.vibekb/install.json` records, for every path VibeKB touched: its ownership
(`vibekb` / `shared`), kind (`payload` / `namespaced` / `managed-block`),
integration name, the installed file hash (or managed-block hash + version),
whether the file existed before VibeKB, and whether VibeKB created the whole file
or only a block. It stores **no absolute paths**. This is what makes upgrades,
`doctor`, migration, and uninstall safe and repeatable.

## Migration behavior (`vibekb migrate .`)

For repositories that already have a pre-2.0 root-level VibeKB install:

- detects legacy files by content signature and known hashes — never by filename
  alone;
- converts a whole-file VibeKB `CLAUDE.md`/`AGENTS.md` pointer into a managed
  block; adds a managed block to a user-authored one while preserving its text;
- relocates the reference docs under `.vibekb/reference/` and removes the
  root-level copies **only when they are byte-identical to VibeKB's** (modified
  copies are left in place and reported);
- removes unmodified root-level runtime (`guide/`, `tools/`, `prompts/`,
  `template/starter/`) after consolidating it under `.vibekb/runtime/`;
- backs up every shared file it rewrites under `.vibekb/backups/`;
- is repeatable and supports `--dry-run`. Ambiguous files are never deleted.

## Doctor (`vibekb doctor`)

Beyond the environment check, `doctor` reports repository-footprint problems and
classifies them (error / warning / info): legacy root-level files, missing
authoritative `.vibekb/` files, duplicate or malformed managed blocks, an
unrecognized `.vibekb/`, a missing or drifted install manifest, and VibeKB-owned
files modified since install. It performs no repairs on its own.

## Uninstall behavior (`vibekb uninstall .`)

- removes VibeKB-owned files recorded in the manifest (everything under
  `.vibekb/` and namespaced adapters);
- strips **only** VibeKB's managed block from shared files, preserving everything
  else; a shared file VibeKB created solely to hold its block is removed, while a
  file that pre-existed VibeKB is kept;
- leaves files with malformed markers untouched and reports them;
- `--keep-knowledge` retains the `.vibekb/` model records and removes only the
  runtime and adapters; `--dry-run` previews everything.

## Transaction safety

Writes go through a temporary file + atomic rename, so a reader never sees a
half-written file. Shared files are backed up before their first edit. The
installation manifest is written last, after the payload and model are in place.
Temporary files are not left behind on success.

## What VibeKB will never do

- Own or overwrite `CLAUDE.md`, `AGENTS.md`, `README`, `.gitignore`, your source,
  or your `docs/`.
- Create `AGENTS.md`/`CLAUDE.md` by default when they do not already exist.
- Write anything at the repository root by default other than a managed block in
  a file you already have (and only when integration is requested/detected).
- Delete a file it cannot positively identify as unmodified VibeKB content.
- Silently repair ambiguous content.
