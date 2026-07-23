# VibeKB 0.2.0 — Safe Repository Integration

Paste-ready GitHub Release body for tag `v0.2.0`.
(The release workflow also generates notes; prefer this body as the primary text.)

---

## Summary

VibeKB 0.2.0 changes how VibeKB installs into **your** repository.

- It **does not overwrite** existing repository-owned files (`README`, your
  source, your `docs/`, and — critically — whole `AGENTS.md` / `CLAUDE.md`
  files).
- Every path VibeKB may write has an **ownership and collision policy**
  (VibeKB-owned under `.vibekb/`, namespaced adapters, or a single managed
  block in a shared file).
- Existing **agent instructions are preserved**; VibeKB only inserts or updates
  a clearly marked block when those files already exist.
- **Unsafe root-level file creation is reduced**: the runtime, reference docs,
  and prompts live under `.vibekb/` instead of polluting the repository root.
- Integrations are **namespaced** (Cursor / Copilot rule files) or **managed**
  (HTML-comment blocks in `AGENTS.md` / `CLAUDE.md`).
- Installation is **repeatable and safer** (`--dry-run`, idempotent managed
  blocks, `.vibekb/install.json` ownership manifest).
- **Migrate**, **uninstall**, **doctor** footprint checks, and **shared-file
  backups** are included.

Full detail: [CHANGELOG.md](https://github.com/cubixmeow-commits/VibeKB/blob/main/CHANGELOG.md) ·
[REPOSITORY_SAFETY.md](https://github.com/cubixmeow-commits/VibeKB/blob/main/docs/REPOSITORY_SAFETY.md)

## Install

```bash
curl -fsSL https://iainreid.dev/vibekb/install.sh | sh
cd /path/to/your/project
vibekb install .
```

Or download a binary below, rename to `vibekb` / `vibekb.exe`, put it on your
`PATH`, then run `vibekb install .`.

PHP 8.2+ is required **after** install for the guide and model commands
(`vibekb check`, `vibekb generate`, the dynamic guide). Install itself does not
require Go or PHP.

### Artifacts

| Artifact | Platform |
| --- | --- |
| `vibekb-darwin-arm64` | macOS Apple Silicon (`install.sh`) |
| `vibekb-darwin-amd64` | macOS Intel (`install.sh`) |
| `vibekb-linux-amd64` | Linux x86_64 (`install.sh`) |
| `vibekb-linux-arm64` | Linux ARM64 (`install.sh`) |
| `vibekb-windows-amd64.exe` | Windows x86_64 (manual) |
| `vibekb-windows-arm64.exe` | Windows ARM64 (manual) |
| `checksums.txt` | SHA-256 of every binary |

## Added

- Ownership / collision policy for every generated path (`docs/REPOSITORY_SAFETY.md`)
- Consolidated install under `.vibekb/` (`runtime/`, `reference/`, `prompts/`, model)
- Managed blocks for existing `AGENTS.md` / `CLAUDE.md` (idempotent; conflict-safe)
- Namespaced adapters for Cursor and GitHub Copilot instructions
- `--dry-run`, `--knowledge-only` / `--no-integrations`, `--integrate`, narrowed `--force`
- `.vibekb/install.json` install manifest (ownership, hashes, provenance)
- `vibekb migrate` for pre-2.0 root-level installs
- `vibekb uninstall` (`--keep-knowledge`, `--dry-run`; backups survive full removal)
- Doctor repository-footprint diagnostics
- Layout-aware PHP core; generated snapshot defaults to `.vibekb/generated/` in target installs

## Changed

- Default install no longer places VibeKB docs/runtime at the repository root
- Upgrades refresh VibeKB-owned files under `.vibekb/` and preserve your model
- Product version **0.2.0** (installer `template_version` remains **2.0.0**, a separate namespace)

## Fixed

- Consolidated `generate` copies guide CSS/JS from the runtime tree (working static snapshot)
- Legacy CLAUDE/AGENTS migration is title+signature gated (no lookalike wipeouts)
- Uninstall preserves shared-file backups instead of deleting them with `.vibekb/`
- Release workflow verifies assets, sorts checksums, and fails closed on missing files

## Migration notes

1. Preview: `vibekb migrate --dry-run .`
2. Apply: `vibekb migrate .`
3. Only unmodified VibeKB root files are removed; anything ambiguous stays and is reported.
4. Prefer `vibekb …` or `php .vibekb/runtime/tools/vibekb.php …` afterward.

Fresh projects: `vibekb install .` is enough — no migrate step.

## Compatibility notes

- `0.x` release — layout and flags may still evolve before 1.0.
- Existing 0.1.0 installs continue to run; migrate is recommended to clean the root.
- `install.sh` supports macOS/Linux arm64/amd64 only; Windows is manual download.
- Self-hosted VibeKB (this repository) still uses root `guide/` / `tools/` by design.

## Known limitations

- `vibekb doctor` uses the current working directory only (no target path argument).
- Doctor footprint checks assume the consolidated layout; the self-hosted VibeKB repo reports missing `.vibekb/runtime/…` paths today.
- Mid-install failure can leave a partial `.vibekb/` (per-file atomic writes, not a multi-file transaction).
- `install.sh` does not yet verify checksums or signatures; no notarization / Authenticode / Homebrew / Winget yet.
