# AGENTS.md — Guidelines for AI agents

This file applies to every coding agent. The full operating rules live in
[CLAUDE.md](./CLAUDE.md); this is the short version plus deployment specifics.

## The product is locked

**VibeKB exists so a vibe coder can open a software project at any point in its
life and understand what the software is currently doing.** Primary promise:
**Understand what your software is doing.**

Software functionality is the primary subject of VibeKB. Repository memory
exists to keep the explanation of that functionality accurate, understandable,
and resistant to drift. **Do not turn VibeKB into a repository-memory product,
documentation generator, code browser, or AI activity log.**

See [PRODUCT.md](./PRODUCT.md) and [CLAUDE.md](./CLAUDE.md).

## The canonical example is SousMeow (real, not bundled)

The `.vibekb/` content models the real **SousMeow** app
(`cubixmeow-commits/dev-portfolio-v2`, `projects/sousmeow`), derived read-only.
SousMeow is not bundled into VibeKB and must never be modified. Re-verify any
functionality claim against the SousMeow source before changing it; do not mark
something `verified-from-source` you did not trace.

## Required workflow for meaningful work

1. Understand current functionality (read `.vibekb/` + the handoff).
2. Record current work in `.vibekb/work/current.md`.
3. Implement.
4. Verify — and record the honest verification state.
5. Update the living software model (`.vibekb/` functionality + system + files +
   diagrams). If a change alters what a diagram shows, update the diagram record
   and its SVG; keep it accessible and label inferred paths.
6. Update repository memory (only meaningful records, each linked to
   functionality).
7. Update the handoff (`.vibekb/work/handoff.md`); refresh `manifest.json`
   provenance if you re-verified against source. If `/docs` is published, run
   `php tools/generate-static.php` to refresh the snapshot.

Do not mark work complete because code was written. Do not present intended or
generated behaviour as verified. See [MAINTENANCE.md](./MAINTENANCE.md).

## Deployment maintenance

Whenever the repository structure changes, new runtime folders are added, the
build output changes, or deployment requirements change, review and update
`.cpanel.yml` as part of the same task. The deployment configuration is part of
the application and must always remain accurate. Never assume the existing
deployment file is still correct.

### Checklist when touching deployment-related structure

1. Re-identify the public entry point and any production build output directory.
2. Update rsync `--exclude` rules for new development-only paths.
3. Protect any new persistent production paths (uploads, storage, databases,
   sessions, server config) from `rsync --delete`.
4. Keep secrets out of the deploy sync (`.env`, local config, credentials).
5. Do not add Node.js or other unsupported build steps for this cPanel shared
   host unless the host tooling is confirmed.
6. Update `DEPLOYMENT.md` when the deploy path, exclusions, or persistent data
   assumptions change.
