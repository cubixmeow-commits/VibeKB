---
id: storage
type: system
title: Storage
summary: There is no runtime database — the model is plain files under `.vibekb/`, generated output is files under `/docs`, and provenance lives in `manifest.json`.
updated: 2026-07-22
---

## What is stored, and where

| Store | What | Written by |
|-------|------|-----------|
| `.vibekb/` | The living model (records + manifests) | Humans and AI agents, by hand |
| `.vibekb/manifest.json` | Provenance and structure metadata | Agents, on re-verification |
| `/docs/` | The static snapshot | `tools/generate-static.php` (generated) |
| `docs/assets/data/search.json` | The static search index | The generator |
| `examples/*/.vibekb/` | Bundled example models | Field tests (rarely changed) |

## No runtime database

VibeKB deliberately has no SQL database, no cache store, no sessions, and no
network at render time. The model is versioned with the code, which is what makes
it review-able in a pull request and deployable by copying files.

## What is generated vs authored

`.vibekb/` and `examples/` are **authored**. `/docs/` is **generated** — never
hand-edited; the drift check flags a `/docs` that differs from a fresh render.
