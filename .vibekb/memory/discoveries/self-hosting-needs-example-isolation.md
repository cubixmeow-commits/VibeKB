---
id: self-hosting-needs-example-isolation
type: discovery
title: Self-hosting forced a clean split between the active model and examples
summary: Dogfooding VibeKB on itself surfaced that the loader keys records by id and loads whatever `.vibekb/` it is pointed at, so an example model and the active model cannot coexist in one root — they must be separate roots.
status: active
verification: verified-from-source
updated: 2026-07-22
functionality: [load-living-model, initialize-in-a-repository]
files: [guide/lib/Content.php, guide/index.php, examples/sousmeow/.vibekb/manifest.json]
changed_model: true
tags: [dogfooding, discovery, architecture]
---

## What we found

While bootstrapping VibeKB on itself, it became clear the loader loads exactly one
content root and keys records by id. Keeping the SousMeow example in the root
`.vibekb/` alongside VibeKB's own records would have merged two unrelated models
and made "what is VibeKB currently doing?" un-answerable.

## Evidence

- `Content` takes a single root and globs `functionality/records/*.md` etc. from
  it; there is no notion of multiple models in one root.
- The guide hard-defaulted to the repository's `.vibekb/`.

## What changed in the model / product

- The SousMeow example moved to `examples/sousmeow/.vibekb/`; the field-test audit
  moved to `examples/field-tests/`.
- A `VIBEKB_CONTENT_ROOT` override (constrained to a `.vibekb` directory) lets the
  same guide and validator preview or check an example without a second app.
- The active root `.vibekb/` now contains only VibeKB's own model.

## Why it matters

An agent entering the repository can no longer mistake another application's model
for the current state of VibeKB — an acceptance criterion for self-hosting.
