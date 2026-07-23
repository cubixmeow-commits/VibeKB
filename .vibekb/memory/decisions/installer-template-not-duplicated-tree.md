---
id: installer-template-not-duplicated-tree
type: decision
title: The template is a manifest plus a generator, not a duplicated file tree
summary: template/ declares the installable payload (manifest.json) and the fresh model comes from a single canonical starter definition (template/starter/) read by both consumers; the installer copies the runtime from the canonical repository files (embedded into the binary at build time) rather than from a second physical copy, so nothing drifts.
status: accepted
verification: verified-from-source
updated: 2026-07-23
functionality: [install-into-a-repository]
files: [template/manifest.json, template/starter/starter.json, embed.go, tools/lib/Starter.php]
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
preserved paths, the generated paths) and the canonical starter definition under
`template/starter/` — not a copy of the runtime. The native installer embeds and
copies the runtime from the canonical repository files at build time (`embed.go`),
never from a second physical copy. The fresh `.vibekb/` model comes from the
single `template/starter/` data definition, read by both the Go installer
(embedded) and `tools/lib/Starter.php` (for `bootstrap`), so there is no second
copy of the starter content either.

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

- `template/` holds the payload definition (`manifest.json`) and the canonical
  starter data (`template/starter/`); neither is a copy of the runtime.
- `template/` is excluded from VibeKB's own drift detection (it is distribution
  metadata, like `docs/` is generated output).
- `template/starter/` is installed into targets so `bootstrap` can repair a
  workspace from the same definition the installer embeds.
- Upgrades refresh exactly the manifest's payload and never the preserved
  `.vibekb/`.

## Update (native installer)

This decision predates the native Go installer but still holds: with
installation moved into the binary (`decision:native-installer-embedded-payload`),
"copy from the canonical files" became "embed the canonical files and copy from
the embed", and the starter definition moved from PHP code into
`template/starter/` data. Both changes keep the one-authoritative-source rule.
