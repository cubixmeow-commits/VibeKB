---
id: project-intent
type: intent
title: Why SousMeow exists
summary: To make AI-assisted work trustworthy — structure, human review, and versioning around the AI you already use — not another AI wrapper.
updated: 2026-07-16
verification: verified-from-source
functionality: [run-recipe, review-quality-checks, export-project-kit]
---

## Outcome it must produce

A maker should be able to take a proven workflow, feed it their own facts, run
it through their own AI, and walk away with a **finished, reviewed deliverable**
they can trust and hand off — without wiring up an API or trusting an
unreviewed black box.

> Verified from source: `README.md`, `docs/PRODUCT_LAW_002_REMOVE_COGNITIVE_LOAD.md`.

## Problem it addresses

Chat AIs are powerful but unstructured. People get inconsistent output, lose the
thread across long sessions, and ship work they never really reviewed. SousMeow
supplies the missing structure: ordered steps, prompts built from stated facts,
per-version human checks, immutable history, and an explicit approval gate.

## The core product law

`docs/PRODUCT_LAW_002_REMOVE_COGNITIVE_LOAD.md` states the product's governing
rule — **Remove Cognitive Load** — enforced as a mandatory "Complexity Gate"
every Cookbook must pass before it ships. Features that add steps or confusion
are rejected on principle.

## What it must not become

- **An AI wrapper.** The moment SousMeow calls an AI itself, it takes on API
  keys, token billing, and black-box output — the exact things it exists to
  avoid. See the `never-calls-ai` decision.
- **A content-authoring CMS.** Cookbooks are curated, versioned seed files that
  must pass the Complexity Gate — not user-generated web content.
