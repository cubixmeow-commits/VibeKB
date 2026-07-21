---
id: run-recipe-flow
type: diagram
title: Run a Cookbook — the core loop
summary: The verified flagship flow from building a prompt through running it in the user's own AI, pasting the response, confirming quality checks, approving an immutable version, and exporting a project kit.
diagram_type: user-journey
group: product-flows
svg: run-recipe-flow.svg
functionality: [run-recipe, build-prompt, paste-response, review-quality-checks, approve-and-version, export-project-kit]
files: []
data: [artifacts, artifact_versions, quality_checks]
warnings: [pasted-response-is-untrusted, read-write-path-coupling]
diagrams: [app-overview, storage-map]
status: implemented
verification: verified-from-source
provenance: Traces the six functionality records of the Runner chain, each verified-from-source. Source evidence — the run-recipe, build-prompt, paste-response, review-quality-checks, approve-and-version and export-project-kit records.
last_verified: 2026-07-16
uncertainty: Step order is the happy path; branch/skip behaviour within a step is described in each record, not this diagram.
created: 2026-07-21
updated: 2026-07-21
---

## What am I looking at?

The end-to-end "Run a Cookbook" loop — the reason SousMeow exists. Teal steps
happen inside SousMeow; amber steps happen in the user's own AI, outside the
app:

1. **Build prompt** from a recipe plus the project's pantry.
2. **Run in AI** — the user pastes the prompt into their own tool.
3. **Paste response** back into SousMeow (stored escaped).
4. **Quality checks** — the human confirms each check.
5. **Approve + version** — the result becomes an immutable artifact version.
6. **Export kit** — the approved work is exported as a project kit.

## Why it matters

This is the most-verified path in the model. If you change any step, this shows
the neighbours you must keep consistent.

## What is uncertain

Nothing in the loop itself is inferred; the two linked warnings mark the places
where a careless edit does damage (untrusted pasted content, and the coupled
Pantry/Artifact read-write paths).
