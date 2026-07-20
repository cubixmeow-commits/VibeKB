---
id: demo-mode-labeling
type: discovery
title: Sample data is labelled everywhere it appears
summary: A pasted "example response" carries source='example' from the version through the review UI, the export manifest, and kit.html — sample data is never presented as real.
status: resolved
verification: verified-from-source
updated: 2026-07-16
functionality: [paste-response, export-project-kit, demo-simulation]
files: [app/Controllers/RunnerController.php, app/Services/ProjectKit.php]
tags: [demo, honesty, provenance]
---

## Discovery

Demo Mode's "paste example response" does not shortcut review — the example is
stored as an ordinary artifact version, but its provenance is carried through
the whole system so it is always visibly sample data.

## Evidence

`pasteExample()` stores the version with `source = 'example'` and a message
marking it sample data. `ProjectKit` propagates that: the per-artifact Markdown
header adds "(sample data from Demo Mode)", the kit.html meta line adds "sample
data", and the manifest notes "sample data" per file.

## Affected functionality

`paste-response`, `export-project-kit`, and `demo-simulation`.

## Consequence

A user (or a portfolio viewer) can always tell reviewed real output from seeded
examples, even inside an exported kit. This is consistent with the product's
"never a black box" intent.

## Did it change the software model?

It confirmed an intended honesty property and is reflected in the related
records.
