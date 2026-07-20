---
id: system-mental-model
type: system
title: The simplest mental model
summary: A Cookbook is a workflow of Recipes; you fill a Pantry, run each Recipe's prompt in your own AI, review and approve the result, then export. SousMeow never calls the AI.
updated: 2026-07-16
verification: verified-from-source
---

## Hold this picture in your head

```
Cookbook (workflow)  →  Recipes (steps)
      │
Pantry (your facts)  →  PromptBuilder  →  prompt you copy
      │                                        │
      │                          run in YOUR AI (SousMeow never does)
      ▼                                        ▼
Artifact versions (immutable)  ←  paste the answer back
      │
Quality Checks (human)  →  Approve (all checks)  →  next Recipe
      │
   all approved  →  Project Kit (zip: Markdown + kit.html + README)
```

## The five nouns

- **Cookbook** — a proven workflow. **Recipe** — one ordered step in it.
- **Pantry** — the facts you enter once; every prompt is built from them.
- **Project** — your run of a Cookbook (owns the Pantry values and Artifacts).
- **Artifact** — the reviewed output of one Recipe, kept as immutable versions.
- **Project Kit** — the exported deliverable.

## The one rule that explains everything

**SousMeow never calls an AI.** It builds the prompt, you run it in the AI you
already pay for, and you paste the answer back. Everything else — versioning,
human Quality Checks, the approval gate, chaining approved Artifacts into later
prompts — exists to make that loop trustworthy.

## What this means for change

Change what a Recipe *asks for* → touch the seed content and maybe the output
contract. Change how an Artifact is *stored or reviewed* → touch the Runner,
`Artifact`, and the schema together. Change the *look* → touch a view. Knowing
which case you are in is most of the work.
