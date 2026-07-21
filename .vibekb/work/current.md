---
id: current-work
type: work
title: Build Explainable Diagrams into VibeKB V1
summary: Turn VibeKB's source-grounded diagrams into explainable diagrams — every node states what it is, every edge states the concrete mechanism connecting its endpoints, every displayed file states why it matters, and the terminal handoff is an external source link.
objective: Add a repository-owned topology model (nodes, edges, edge mechanisms, per-node/edge files with reasons, repository locations, verification) that projects the existing living software model into diagrams that teach how the software works before any click, rendered identically in the dynamic guide and the static snapshot, without JavaScript.
requested_by: Project owner
status: completed
verification_state: verified-manually
updated: 2026-07-21
affected_functionality: []
expected_files:
  - guide/lib/helpers.php
  - guide/lib/Content.php
  - guide/lib/search.php
  - guide/templates/diagrams.php
  - guide/templates/functionality-detail.php
  - guide/assets/css/guide.css
  - guide/assets/js/guide.js
  - tools/validate.php
  - tools/generate-static.php
  - .vibekb/diagrams/topology/*.json
  - .vibekb/diagrams/records/*.md
  - .vibekb/diagrams/assets/*.svg
  - SCHEMA.md, PRODUCT.md, INITIALIZE.md, MAINTENANCE.md, prompts/INTEGRATE_VIBEKB.md
data_impact: None to SousMeow (analysed read-only, never modified). VibeKB product code + repository-owned content only.
risks:
  - Turning VibeKB into a code browser / graph editor / IDE (explicitly out of bounds).
  - Drawing edges without a concrete, stateable mechanism (coincidence edges).
  - Marking an inferred relationship as verified.
  - Fabricating source line numbers or claiming an inferred location is verified.
  - The dynamic guide and static snapshot drifting apart (must share one template).
  - Front-matter fragility if topology were forced into scalar/list front matter.
---

## What the user asked for

Build **Explainable Diagrams** as a real V1 feature. A diagram is a visual
projection of the existing living software model. It must teach at a glance
(labelled nodes + mechanism-labelled edges reading like a sentence) and reward
selection (per-node and per-edge explanations: purpose, repository location,
relevant files with reasons, verification, uncertainty, and an external source
link as the terminal "show me the implementation"). Everything must justify its
own existence; every click answers the reader's next question. The experience
must work without JavaScript and render identically in the dynamic guide and the
static `/docs` snapshot.

## Current behaviour (before)

- `.vibekb/diagrams/` holds diagram records (Markdown + front matter) and
  repository-owned SVGs with accessible `<title>`/`<desc>`.
- `Content.php` loads records, inlines the SVG, and resolves back-links to
  functionality, warnings, and other diagrams. `diagrams.php` renders the SVG
  plus a prose caption and relationship rails.
- A diagram is a picture plus narrative. There is **no structured topology** — no
  node/edge records, no edge mechanisms, no per-file reasons, no source links,
  and no way to select a node or edge and learn about it.

## Proposed behaviour (after)

- A new optional **topology** file per diagram:
  `.vibekb/diagrams/topology/<diagram-id>.json` (referenced by the record's
  `topology:` field). Simple, human-readable, AI-editable JSON — no YAML, no
  database, no build step. Contains `version`, `nodes[]`, `edges[]`.
- A single **controlled edge-mechanism vocabulary** and a **file-role
  vocabulary**, defined once in `guide/lib/helpers.php` and used by loading,
  validation, and documentation. Vague mechanisms (relates-to, works-with,
  interacts-with, associated-with, connected-to) are rejected by the validator.
- Two edge verification states in V1: verified (solid line) and inferred (dashed
  line). Both must pass the explainability gate; verification communicates how
  well the mechanism is grounded, not whether an unexplained edge is allowed.
- `Content.php` loads and normalises topology, resolves node/edge functionality,
  warnings, and files (reusing `important-files.json` metadata), and generates
  immutable GitHub source links from manifest provenance (commit-pinned; no
  fabricated line numbers).
- `diagrams.php` renders complete semantic node/edge explanation sections with
  stable anchors (`#node-<id>`, `#edge-<id>`) that the SVG's
  `data-vibekb-node` / `data-vibekb-edge` markers point at. A small,
  unobtrusive vanilla-JS enhancement adds selection, dimming, and a synced
  detail panel; nothing essential is hidden behind it.
- Validation (loader + `tools/validate.php`) enforces the topology contract and
  the SVG-marker↔topology-id mapping in both directions. Malformed topology is
  reported as a Reference diagnostic; legacy diagrams without topology still
  render.

## Affected VibeKB functionality

VibeKB's own guide app (the product) and its documentation. The SousMeow model
is upgraded only enough to demonstrate the feature honestly on representative
diagrams (request-flow, run-recipe-flow), re-verified against the recorded
source commit; SousMeow itself is never modified.

## Verification plan

- `php -l` across changed `guide/` and `tools/` files.
- `php tools/validate.php` reports zero errors, including the new topology and
  SVG-marker checks; a deliberately malformed topology fixture produces a useful
  diagnostic and a non-zero exit.
- `php tools/generate-static.php` regenerates `/docs`; every node/edge anchor,
  internal link, and commit-pinned source link resolves.
- Manually confirm: verified edges render solid, inferred edges render dashed,
  verification is also stated in text; keyboard users reach nodes/edges;
  no-JavaScript users can read every explanation; the layout holds at mobile
  widths; legacy diagrams without topology still render; search links reach the
  correct diagram anchor; no source outside VibeKB was modified.
