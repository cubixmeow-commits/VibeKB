---
id: current-state
type: project
title: What VibeKB does right now
summary: A working V1 that loads a repository-owned model, renders it as a dynamic guide and a static snapshot, validates it, ships explainable diagrams, and (new in this change) is self-hosted with a CLI that helps agents run the maintenance lifecycle and detect drift.
updated: 2026-07-22
---

## Implemented and verified

- **Content model** — `.vibekb/` is loaded, parsed (front matter + a pragmatic
  Markdown subset), relationship-resolved, and validated by
  `guide/lib/Content.php`. All filesystem access is confined to the content root.
- **Dynamic guide (Mode A)** — `guide/index.php` routes views by `?view=` (no
  rewrite rules) and renders one template set. Every page carries objective
  provenance.
- **Static snapshot (Mode B)** — `tools/generate-static.php` renders the *same*
  templates into `/docs` with relative, subpath-safe links.
- **Validation** — the loader plus `tools/validate.php` report structural errors
  headlessly and gate generation and CI.
- **Explainable diagrams** — diagrams can carry a repository-owned topology
  (`diagrams/topology/*.json`): nodes with purposes, edges with controlled
  mechanisms, files with reasons, and commit-pinned source links, usable without
  JavaScript. `tools/test-topology.php` proves malformed topology is reported,
  not crashed.
- **Self-maintenance CLI (new)** — `tools/vibekb.php` gives agents one entry
  point for the lifecycle: `status` (session start), `check` (drift + validate +
  snapshot sync), `affected` (changed files → likely functionality), `validate`,
  and `generate`.

## Self-hosted

This repository now contains VibeKB's own model. The dynamic guide and `/docs`
both render VibeKB explaining VibeKB. Example models of other apps are isolated
under `examples/` and are not the active model.

## Honest limitations

- **Not automatic.** VibeKB detects that files changed; it does not understand
  what a change *means*. Reconciling the model is an agent's job.
- **Affected-functionality discovery is a heuristic**, derived from the `files[]`
  back-links already in the model. A changed file with no back-link is reported
  as "unmapped," not silently ignored, but the mapping is never assumed perfect.
- **Cursor discovery** is provided via `.cursor/rules/` and `AGENTS.md`; it is
  `inferred` that a fresh Cursor session will follow it, not runtime-verified
  here.
- **The cPanel deploy target** is described from `.cpanel.yml`; the live host is
  not exercised in this environment.
- The Markdown renderer supports a documented subset, not full CommonMark.
