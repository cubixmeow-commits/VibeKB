---
id: identity
type: project
title: VibeKB
one_liner: A living software model that explains what your application is currently doing, organized around functionality, kept honest as AI coding agents change the code.
summary: A repository-resident living software model that explains what a software project is currently doing, organized around functionality, and keeps that explanation honest and resistant to drift while AI coding agents change the code.
updated: 2026-07-22
---

## What VibeKB is

VibeKB is a **living software model** that lives inside a repository (in
`.vibekb/`) and explains, in plain language, **what the software is currently
doing**: what functionality exists, how it works, where it is implemented, how
data moves, what an AI agent is changing right now, what was verified and how,
and why the important decisions were made.

It renders that model as a website through one template set in two modes — a
dynamic PHP guide (`guide/`) and a static snapshot (`/docs`) — but the website
is a *view* of the model, not the product. The product is the model and the
workflow that keeps it true.

## What VibeKB is not

VibeKB is **not** a documentation generator, a repository-memory archive, a code
browser, or an AI activity log. Its primary unit is **functionality** (the
things software does), not files, decisions, or sessions. The file tree supports
understanding; it is not the subject.

## This model describes VibeKB itself

The `.vibekb/` model in this repository is VibeKB's **own** model: VibeKB
explaining VibeKB. The functionality records below describe VibeKB's real
components — the content loader, the guide renderer, the static generator, the
validator, the explainable-diagram system, and the self-maintenance CLI — traced
from the source in this repository.

Example models of *other* applications (for demonstration and field testing)
live under `examples/`. They are not the active model and must never be mistaken
for the current state of VibeKB.
