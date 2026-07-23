---
id: native-installer-embedded-payload
type: decision
title: Installation is native to the Go binary, from an embedded payload
summary: The installer is implemented in Go and embeds the runtime payload and a canonical starter definition, so `vibekb install` runs without PHP and without the source repository. PHP is required only to run the installed guide. install.php becomes a compatibility wrapper; the starter model becomes shared data.
status: accepted
verification: verified-from-source
updated: 2026-07-23
functionality: [install-into-a-repository, bootstrap-workspace]
files: [embed.go, internal/installer/installer.go, template/starter/starter.json, install.php]
tags: [installer, go, embed, native, distribution, dry]
---

## Context

Under "Go front-end, PHP core", installation was still `php install.php`. But
installing is almost entirely filesystem work — copying a declared payload and
writing starter files — and requiring PHP just to *install* is the opposite of
the desired experience (`brew install vibekb && vibekb install …`). Installing a
runtime and requiring that runtime are different responsibilities.

## Decision

Move installation into the Go binary and embed everything it needs:

- `embed.go` (at the module root, because embed patterns cannot escape their
  directory) embeds `template/manifest.json`, the runtime payload (`guide/`,
  `tools/`, `prompts/`, `.cursor/`, the docs), and `template/starter/`.
- `internal/installer` parses the embedded manifest (the single source of truth
  for the file set — no second manifest format), copies from the embedded FS,
  scaffolds `.vibekb/` from the embedded starter definition, and verifies
  natively. No PHP process is launched at any point.
- The starter model becomes language-neutral **data** under `template/starter/`
  (`starter.json` + a `files/` tree with `{{DATE}}` / `{{PROJECT_NAME_JSON}}`
  tokens), read by both the Go installer (embedded) and `tools/lib/Starter.php`
  (on disk, for `bootstrap`). `template/starter/` is installed into targets so
  `bootstrap` keeps working there.
- `install.php` becomes a thin wrapper that forwards to `vibekb install`, so there
  is only one installer implementation.

## Alternatives considered

- **Keep delegating to install.php** — rejected: installation would still require
  PHP and a live clone, blocking the single-binary distribution goal.
- **Reimplement the starter as Go code** — rejected: it would duplicate the
  starter content (Go copy + PHP copy) and drift. Making it shared data keeps one
  definition.
- **Embed by duplicating the runtime under template/** — rejected: a second
  physical copy of `guide/`/`tools/` would drift (the very thing
  `installer-template-not-duplicated-tree` avoids). `embed.go` embeds the
  canonical files directly at build time.

## Reason

Installing should be one self-contained, PHP-free step, while the model loader,
generator, and guide stay PHP (unchanged). Embedding the canonical payload and a
single shared starter definition achieves that with no duplication and no second
source of truth. It is verified: a native install with PHP removed from PATH
produces a workspace that PHP `bootstrap` and `validate` then accept, byte-identical
to PHP's own scaffold.
