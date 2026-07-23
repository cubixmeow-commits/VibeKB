# REPOSITORY_FOOTPRINT_AUDIT.md — Historical audit of the pre-2.0 installer

> **Historical (Phase 1 deliverable, pre-2.0).** This document inventories the
> *old* root-level installer footprint that the repository-safety redesign
> replaced. It is kept as evidence of what was wrong and why the redesign
> happened. **It does not describe current behavior.** For the live ownership
> model, install/migrate/uninstall rules, and guarantees, see
> [REPOSITORY_SAFETY.md](./REPOSITORY_SAFETY.md).
>
> Produced by reading the installer and tooling source as it existed before the
> consolidation: `internal/installer/*.go`, `internal/cli/*.go`,
> `internal/phpcore/*.go`, `embed.go`, `template/manifest.json`, `install.php`,
> `tools/*.php`, `guide/index.php`.

## How installation worked then (pre-2.0 mechanics)

1. `vibekb install [target]` runs `internal/installer.Run` (Go, no PHP). The
   legacy `php install.php` is a thin wrapper that forwards to the same binary.
2. The set of installed paths is **not** hard-coded — it is read from
   `template/manifest.json`, which is embedded into the binary (`embed.go`) and
   parsed by `loadManifest()`.
3. For every manifest `payload` entry, `buildPlan()` walks the embedded bytes
   and computes a per-file action: `create` (absent), `replace`
   (upgrade/`--force`), or `skip` (exists, not forced → recorded as *blocked*).
4. Files are written **at the same repository-root-relative path** they occupy
   in VibeKB's own repo (`writeEmbedded(it.embedPath, target/it.embedPath)`).
   There is no source→destination remapping today — so VibeKB's internal layout
   *is* the layout it imposes on every target repo.
5. `.vibekb/` is scaffolded from `template/starter/` (dirs from `starter.json`,
   files from `files/` with `{{DATE}}`/`{{PROJECT_NAME_JSON}}` tokens).
6. `writeState()` records `.vibekb/.installer.json`.

Key consequence: **VibeKB installs 7 root-level Markdown files and 5 top-level
directories into the target's root**, several of which are names commonly owned
by the user, another tool, or a framework.

---

## Footprint inventory (pre-2.0 behavior)

Legend for **Ownership**: `V` VibeKB-owned · `S` shared (may pre-exist, others
may own) · `R` repository-owned. **Collision risk**: how likely the name already
exists and belongs to someone else.

### A. Root-level Markdown files (manifest `payload.docs`)

| # | Path | Created by | Ownership | If it already exists | Collision risk | Recommended future behavior |
|---|------|-----------|-----------|----------------------|----------------|-----------------------------|
| 1 | `CLAUDE.md` | `install` (docs) | **S** — Claude Code / user own this | `skip` (kept) unless `--force` → **overwritten** | **Critical** — canonical Claude Code instructions file | Never own. Optional managed block only, opt-in. Move VibeKB's own guidance under `.vibekb/reference/`. |
| 2 | `AGENTS.md` | `install` (docs) | **S** — Codex / user own this | `skip` unless `--force` → **overwritten** | **Critical** — cross-tool agent instructions convention | Same as CLAUDE.md: managed block, opt-in. |
| 3 | `PRODUCT.md` | `install` (docs) | V (but root-polluting) | `skip` unless `--force` | High — generic name | Relocate to `.vibekb/reference/PRODUCT.md`. |
| 4 | `SCHEMA.md` | `install` (docs) | V (root-polluting) | `skip` unless `--force` | High — generic name | Relocate to `.vibekb/reference/SCHEMA.md`. |
| 5 | `INITIALIZE.md` | `install` (docs) | V (root-polluting) | `skip` unless `--force` | Medium | Relocate to `.vibekb/reference/INITIALIZE.md`. |
| 6 | `MAINTENANCE.md` | `install` (docs) | V (root-polluting) | `skip` unless `--force` | High — generic name | Relocate to `.vibekb/reference/MAINTENANCE.md`. |
| 7 | `INSTALLER.md` | `install` (docs) | V (root-polluting) | `skip` unless `--force` | Medium | Relocate to `.vibekb/reference/INSTALLER.md`. |

All seven are **owned** by VibeKB today: `--force` replaces them wholesale, and
a non-force upgrade (`isUpgrade`) also replaces them. That is data loss for
CLAUDE.md/AGENTS.md and root clutter for the rest.

### B. Top-level directories (manifest `payload.runtime` + `payload.agent`)

| # | Path | Created by | Ownership | If it already exists | Collision risk | Recommended future behavior |
|---|------|-----------|-----------|----------------------|----------------|-----------------------------|
| 8 | `guide/` (whole PHP app: `index.php`, `lib/`, `templates/`, `assets/`) | `install` (runtime) | V | per-file `skip`/`replace` | Medium–high — `guide/` is a common docs dir | Relocate to `.vibekb/runtime/guide/`. |
| 9 | `tools/` (`vibekb.php`, `generate-static.php`, `validate.php`, `test-topology.php`, `lib/Starter.php`) | `install` (runtime) | V | per-file `skip`/`replace` | **High** — `tools/` is extremely common | Relocate to `.vibekb/runtime/tools/`. |
| 10 | `template/starter/` (+ `template/starter/files/**`) | `install` (runtime) | V | per-file `skip`/`replace` | **High** — `template/` collides with app templates | Relocate to `.vibekb/runtime/template/starter/`. |
| 11 | `prompts/` → `prompts/INTEGRATE_VIBEKB.md` | `install` (agent) | V | per-file `skip`/`replace` | High — `prompts/` common in AI/LLM repos | Relocate to `.vibekb/prompts/`. |
| 12 | `.cursor/rules/vibekb.mdc` | `install` (agent) | V, **namespaced** | per-file `skip`/`replace` | Low — the *file* is namespaced; `.cursor/` dir is shared | Keep. This is the correct model: a namespaced file inside a shared dir. Never touch other `.cursor/` files. |

### C. The workspace and its state file (`scaffoldWorkspace` + `writeState`)

| # | Path | Created by | Ownership | If it already exists | Collision risk | Recommended future behavior |
|---|------|-----------|-----------|----------------------|----------------|-----------------------------|
| 13 | `.vibekb/` (dirs from `starter.json`; `manifest.json`, `project/*.md`, `functionality/index.json`, `system/*.md`, `work/*.md`, `files/important-files.json`, `diagrams/index.json`, `.htaccess`) | `install` (scaffold), `php tools/vibekb.php bootstrap` | **V (exclusive)** | Preserved unless `--force`; missing files repaired | Low if absent; **unhandled** if a *foreign* `.vibekb/` exists | Authoritative VibeKB home. Add unrecognized-collision detection (a `.vibekb/` we did not create). |
| 14 | `.vibekb/.installer.json` | `writeState` | V (state) | Overwritten every run (by design) | Low | Replace with a richer `.vibekb/install.json` manifest (ownership + hashes + provenance). |
| 15 | `.vibekb/.htaccess` | scaffold (`starter/files/.htaccess`) | V | kept unless `--force` | Low | Keep under `.vibekb/`. |

### D. Generated output (produced later, not by the installer)

| # | Path | Created by | Ownership | If it already exists | Collision risk | Recommended future behavior |
|---|------|-----------|-----------|----------------------|----------------|-----------------------------|
| 16 | `docs/` (static site: `index.html`, per-page HTML, `assets/css`, `assets/js`) | `php tools/vibekb.php generate` / `tools/generate-static.php` | V (generated) | Overwrites its own managed subtree; leaves other files | **High** — `docs/` is one of the most common repo dirs; generator writes `index.html` + `assets/` into it | For installed repos, default generated output to `.vibekb/generated/` (opt in to root `docs/`). Never assume `docs/` is VibeKB's. |

### E. Paths VibeKB reads/assumes but does not write

| Path | Command | Notes |
|------|---------|-------|
| `.git/` | `repoSignals`, drift via `git -C` | Read only. Used to decide "looks like a project" and for drift detection. |
| `README*`, `src/`, `package.json`, … | `repoSignals` | Read only, to sanity-check the target. |
| `VIBEKB_CONTENT_ROOT`, `VIBEKB_DOCS_OUT`, `VIBEKB_PHP` (env) | guide/tools | Read only. Override content root / docs output / php binary. |

### F. What the manifest deliberately does **not** install (`not_installed`)

`index.php`, `assets/`, `.vibekb` (source's own), `examples/`, `docs/`,
`.cpanel.yml`, `DEPLOYMENT.md`, `TOKEN_ECONOMICS.md`, `BUILD_REPORT.md`,
`ARCHITECTURE.md`, `RELEASE.md`, `.github/`, `install.php`,
`template/manifest.json`, `template/README.md`, `cmd/`, `internal/`, `go.mod`,
`go.sum`. These are VibeKB-repo-only. This list is correct and should be
preserved by the new manifest.

---

## Commands that touch the filesystem (audit of operations)

| Command | Writes | Deletes | Notes |
|---------|--------|---------|-------|
| `vibekb install` (Go) | payload files (create/replace), `.vibekb/**` scaffold, `.vibekb/.installer.json` | none | `--force` replaces existing payload + resets `.vibekb/`. No backups. No transaction — a mid-run failure leaves a partial install. |
| `vibekb install --dry-run` | nothing | nothing | Prints plan + full file list. Does **not** preview shared-file/managed-block impact (there is none today). |
| `vibekb install --upgrade` | replaces payload; preserves `.vibekb/`; repairs missing starter files | none | Auto-detected when `.vibekb/.installer.json` exists. |
| `php install.php` | — | — | Forwards to the `vibekb` binary; no independent FS writes. |
| `php tools/vibekb.php bootstrap` | missing `.vibekb/` dirs & starter files (never overwrites existing) | none | Deterministic repair from `template/starter`. |
| `php tools/vibekb.php generate` | `docs/**` (or `VIBEKB_DOCS_OUT`) | prunes its own previously-generated pages under the output dir | Regenerates the static snapshot. |
| `php tools/vibekb.php check` | temp dir under system temp for a generate-diff | temp cleaned | Read-only w.r.t. the repo. |
| `vibekb doctor` (Go) | nothing | nothing | Environment probe only. No repository repair, no footprint checks. |
| **uninstall** | — | — | **Does not exist.** No way to cleanly remove VibeKB. |
| **migrate** | — | — | **Does not exist** as a distinct command; `--upgrade` refreshes in place at the same (root) paths. |
| **repair** | (see `bootstrap`) | — | Workspace-only; does not reconcile the payload/manifest. |

---

## Risk summary (what makes users afraid)

1. **Owning `CLAUDE.md` and `AGENTS.md`.** These are the canonical instruction
   files for Claude Code and Codex/other agents. VibeKB creates them if absent
   (claiming a name it does not own) and **overwrites them on `--force` or
   upgrade**. This is the single scariest behavior — potential silent loss of a
   user's carefully authored agent instructions.
2. **Heavy root pollution.** 7 root `.md` files + `tools/`, `guide/`,
   `template/`, `prompts/`. `tools/` and `template/` are especially likely to
   already exist and mean something else in the target.
3. **No ownership record with evidence.** `.installer.json` lists installed
   paths but stores no hashes, no "existed before" flag, no managed-block
   accounting — so an upgrade cannot tell a user-edited file from an untouched
   one, and uninstall is impossible to do safely.
4. **`--force` is a blunt instrument.** It overwrites *any* pre-existing payload
   file, including root `CLAUDE.md`/`AGENTS.md`, with no backup and no
   distinction between "VibeKB file" and "user's file with a colliding name."
5. **`docs/` collision.** The generator writes `index.html` + `assets/` into a
   root `docs/` — a directory most repos already use for their own docs.
6. **No migration, no uninstall, no footprint doctor.** Once installed, there is
   no supported way to consolidate, relocate, verify, or cleanly remove the
   footprint.
7. **Foreign `.vibekb/` unhandled.** A pre-existing `.vibekb/` that VibeKB did
   not create is treated as "preserve/repair," not as a collision to report.
8. **No transaction safety.** A failure partway through `install` leaves the
   repo half-written with no rollback.

---

## Old footprint (root of target repo), at a glance

```
CLAUDE.md            AGENTS.md          PRODUCT.md      SCHEMA.md
INITIALIZE.md        MAINTENANCE.md     INSTALLER.md
guide/               tools/             template/starter/   prompts/
.cursor/rules/vibekb.mdc
.vibekb/             .vibekb/.installer.json
docs/                (generated later)
```

12 root-level entries created by `install` (7 files + 5 dirs), plus `.vibekb/`,
plus a generated `docs/`. Of these, **`CLAUDE.md` and `AGENTS.md` are
shared/user-owned and must never be owned**, and **all of `PRODUCT.md`,
`SCHEMA.md`, `INITIALIZE.md`, `MAINTENANCE.md`, `INSTALLER.md`, `guide/`,
`tools/`, `template/`, `prompts/`, `docs/` should leave the root**.

See [REPOSITORY_SAFETY.md](./REPOSITORY_SAFETY.md) for the redesigned ownership
model, the consolidated `.vibekb/` layout, and the safe installation, migration,
doctor, and uninstall behavior that resolve every row above. That document — not
this audit — is the source of truth for what VibeKB does now.
