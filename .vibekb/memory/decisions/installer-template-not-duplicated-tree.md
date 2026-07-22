---
id: installer-template-not-duplicated-tree
type: decision
title: The template is a manifest plus a generator, not a duplicated file tree
summary: template/ declares the installable payload (manifest.json) and the starter model is produced by tools/lib/Starter.php; the installer copies the runtime from the canonical repository files rather than from a second physical copy under template/, so nothing drifts.
status: accepted
verification: verified-from-source
updated: 2026-07-22
functionality: [install-into-a-repository]
files: [template/manifest.json, install.php, tools/lib/Starter.php]
tags: [installer, template, dry, self-hosting]
---

## Context

VibeKB is self-hosted: the active `.vibekb/` describes the files at the
repository root (`guide/`, `tools/`, …). Physically duplicating those into
`template/guide/`, `template/tools/`, etc. would create a second copy that
instantly drifts from the canonical one and that VibeKB's own drift detection
would then have to track.

## Decision

`template/` contains a declarative `manifest.json` (the payload list, the
preserved paths, the generated paths) and documentation — not a copy of the
runtime. `install.php` reads the manifest and copies the runtime from the
canonical repository files present in the VibeKB clone. The fresh `.vibekb/`
model is produced programmatically by `tools/lib/Starter.php`, the single source
of truth shared with `bootstrap`, so there is no second copy of the starter
content either.

## Alternatives considered

- **A full physical `template/guide`, `template/tools`, … tree** — rejected:
  guaranteed drift in a self-hosted repo, and a large duplicated surface to
  maintain.
- **A hard-coded copy list inside install.php** — rejected: the set of installed
  files would be invisible and hard to change; the manifest makes it explicit and
  upgrade-aware.

## Reason

The installer must know exactly which files belong to VibeKB and keep that
knowledge in one authoritative place. A manifest plus a generator expresses that
without duplicating the payload.

## Consequences

- `template/` is intentionally small; the payload definition lives in
  `template/manifest.json` and the starter definition in `tools/lib/Starter.php`.
- `template/` is excluded from VibeKB's own drift detection (it is distribution
  metadata, like `docs/` is generated output).
- Upgrades refresh exactly the manifest's payload and never the preserved
  `.vibekb/`.
