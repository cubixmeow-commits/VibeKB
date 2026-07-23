---
id: deploy-and-stay-portable
type: functionality
title: Deploy and stay portable
area: deployment
summary: The guide deploys to plain PHP 8.2 shared hosting via `.cpanel.yml` with no build step, no database, and no rewrite rules; query-string routing and relative static links let it run at a web root or in a subfolder, and the deploy config is treated as part of the application.
status: implemented
verification: verified-from-source
user_facing: false
trigger: A cPanel Git deploy runs the rsync task in .cpanel.yml; or a static host serves /docs.
updated: 2026-07-22
tags: [deployment, cpanel, portability]
files: [.cpanel.yml, DEPLOYMENT.md, guide/lib/UrlStrategy.php, index.php]
reads: []
writes: []
depends_on: [render-guide, generate-static-snapshot]
related_memory: [constraint:no-build-step-portable, decision:two-modes-one-source, change:homepage-compatibility-section, change:homepage-native-installer-copy, change:homepage-voice-pass, change:homepage-drop-no-go-php-claims]
---

## In one sentence

VibeKB ships as ordinary PHP plus generated static files, so it deploys by
copying files — the deployment configuration is maintained alongside the code it
deploys.

## Current behavior

`.cpanel.yml` rsyncs the runtime paths (`index.php`, `assets/`, `guide/`,
`.vibekb/`) into a cPanel public folder, excluding development-only paths
(`tools/`, `prompts/`, `docs/`, authoring docs, examples). Query-string routing
means no rewrite rules are required and the guide works in a subfolder;
`StaticUrlStrategy` emits relative links so `/docs` works at a web root or under a
repository subpath (GitHub Pages). `.vibekb/` is the content and must deploy;
`examples/` and tooling need not.

## Implementation map

- `.cpanel.yml` — the deploy sync and exclusions.
- `DEPLOYMENT.md` — the deploy contract and persistent-path rules.
- `guide/lib/UrlStrategy.php` — relative vs dynamic URLs.

## Current state

- **Status:** implemented. **Verification:** verified-from-source for the config
  itself (paths and exclusions read against the repository structure). The live
  cPanel host is **inferred** — it is not exercised in this environment.

## Use caution

Whenever repository structure or runtime folders change, update `.cpanel.yml` and
`DEPLOYMENT.md` in the same change. Never let secrets into the deploy sync.

## Why it works this way

Portability (no build, no DB, subfolder-safe) is a hard product constraint;
treating the deploy config as code keeps it accurate as the structure evolves.
