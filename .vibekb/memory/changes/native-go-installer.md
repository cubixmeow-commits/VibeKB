---
id: native-go-installer
type: change
title: Make installation native to the Go CLI (embedded payload, no PHP)
summary: Reimplemented installation inside the `vibekb` Go binary, which embeds the runtime payload and a canonical, language-neutral starter definition. `vibekb install` now copies files and scaffolds a fresh `.vibekb/` with no PHP process and no live source clone. install.php became a thin compatibility wrapper, and tools/lib/Starter.php now reads the same starter data so there is one definition, not two.
status: implemented
verification: verified-from-source
updated: 2026-07-23
functionality: [install-into-a-repository, bootstrap-workspace, run-the-developer-cli]
files: [embed.go, internal/installer/installer.go, internal/installer/console.go, install.php, tools/lib/Starter.php, template/starter/starter.json, template/manifest.json]
tags: [installer, go, embed, native, starter, change]
---

## Before

`vibekb install` delegated to `php install.php`, and the starter workspace was
defined as PHP heredocs inside `tools/lib/Starter.php`. Installing therefore
required a PHP runtime and the VibeKB source clone to be present.

## After

- The starter definition is now **data**: `template/starter/` holds a
  `starter.json` directory list and a `files/` tree with `{{DATE}}` and
  `{{PROJECT_NAME_JSON}}` tokens. It is the single canonical definition.
- `tools/lib/Starter.php` reads that data (byte-identical scaffold output to
  before), so `bootstrap` is unchanged and no starter content is duplicated.
- `template/starter/` is installed into targets (added to the manifest payload)
  so `bootstrap` can still repair a workspace there.
- `embed.go` (at the module root) embeds the manifest, the runtime payload, and
  `template/starter/` into the binary.
- `internal/installer` performs a fully native install: parse the embedded
  manifest, plan, copy from the embedded FS, scaffold `.vibekb/` from the embedded
  starter, write `.vibekb/.installer.json`, and verify natively — no PHP.
- `install.php` is now a compatibility wrapper that forwards to `vibekb install`
  (or prints how to get the binary). There is one installer implementation.

## Impact

Installing VibeKB no longer needs PHP or a live clone — the entry point is a
single self-contained binary, ready for brew/winget/curl packaging. PHP is
required only to run the installed guide. The manifest stays the single source of
truth for the file set, and the starter model has exactly one definition read by
both Go and PHP. Verified end-to-end: native install with PHP removed from PATH,
then PHP `bootstrap`/`validate` on the installed target, plus dry-run, upgrade,
force-reset, and self-hosted-refusal paths.
