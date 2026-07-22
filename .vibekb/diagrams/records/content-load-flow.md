---
id: content-load-flow
type: diagram
title: How the model is loaded and validated
summary: The read path from repository files through the loader into the in-memory model, and how validation feeds the Reference view and CI.
diagram_type: data-flow
group: internals
svg: content-load-flow.svg
topology: content-load-flow.json
functionality: [load-living-model, parse-records, resolve-relationships, validate-model]
files: [guide/lib/Content.php, guide/lib/FrontMatter.php, guide/templates/reference.php]
data: []
warnings: []
diagrams: [vibekb-architecture]
status: implemented
verification: verified-from-source
provenance: Traced from source — Content::load() and validate() in guide/lib/Content.php, the parser in guide/lib/FrontMatter.php and guide/lib/Markdown.php, and the Reference view. All edges are verified-from-source.
last_verified: 2026-07-22
uncertainty: None material — the pipeline is traced end to end in Content.php.
created: 2026-07-22
updated: 2026-07-22
---

## What am I looking at?

The one-way read path. An **entry point** (the guide or a tool) delegates to the
**loader**, which **reads** the repository's content files, **creates** the
resolved in-memory **model**, and **calls validation**. Validation **emits** the
issues shown in the **Reference view** and used by CI. Nothing on this path writes
back to `.vibekb/`.

## Why it matters

It shows why "it validates locally" and "it passes CI" cannot diverge: the guide,
the generator, and the validator all run this exact pipeline. It is also where the
security invariant lives — every file read is confined to the content root.

## What is uncertain

None material — `load()` and `validate()` are traced end to end in
`guide/lib/Content.php`, so every edge is verified.
