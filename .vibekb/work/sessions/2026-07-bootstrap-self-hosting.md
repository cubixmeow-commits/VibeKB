---
id: 2026-07-bootstrap-self-hosting
type: session
title: Bootstrap VibeKB self-hosting
date: 2026-07-22
verification: verified-manually
functionality: [start-work-session, detect-drift, find-affected-functionality, load-living-model, generate-static-snapshot]
files: [tools/vibekb.php, guide/index.php, tools/validate.php, .vibekb, examples]
change: bootstrap-vibekb-self-hosting
---

## Session summary

Turned VibeKB into a self-hosted VibeKB model with an active maintenance
lifecycle. Read the full repository (product, schema, loader, guide, generator,
validator, topology, agent docs, deployment) before implementing, then:

1. Moved the SousMeow model and the StopPR field-test audit under `examples/` and
   added a constrained `VIBEKB_CONTENT_ROOT` override so they stay usable.
2. Authored VibeKB's own `.vibekb/` model grounded in the repository source.
3. Built `tools/vibekb.php` (status / check / affected / validate / generate) and
   extended `tools/validate.php` to accept a content root.
4. Unified agent instructions around a canonical, repository-owned workflow and a
   discoverable CLI; added a CI workflow.
5. Regenerated `/docs` and validated the whole model.

## Dogfooding notes

Used the lifecycle while building it. The most useful discovery
(`self-hosting-needs-example-isolation`) came directly from running the loader
against a mixed root. The `affected` command made it obvious which self-model
records each tooling change touched.
