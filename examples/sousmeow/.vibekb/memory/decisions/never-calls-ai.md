---
id: never-calls-ai
type: decision
title: SousMeow never calls an AI itself
summary: The product builds prompts and reviews output, but the user runs the prompt in their own AI — no API keys, no token billing, no black box.
status: accepted
verification: verified-from-source
updated: 2026-07-16
functionality: [build-prompt, paste-response, run-recipe, demo-simulation]
files: [app/Services/PromptBuilder.php, README.md]
tags: [architecture, product]
---

## Context

Most "AI products" wrap a model API. That brings API keys, per-token cost and
markup, provider lock-in, and output the user never really reviews.

## Decision

SousMeow never makes an AI call. It builds a precise prompt, the user runs it in
the AI they already pay for, and the user pastes the answer back for structured
human review.

## Alternatives considered

- **Call a model API** — rejected: it adds keys, billing, and a black box, the
  exact things the product exists to avoid.

## Reason

The value proposition is structure, review discipline, versioning, and a
finished deliverable — not model access. It also keeps the app cheap to run on
shared hosting with no secrets to leak.

## Consequences

- Every "example" in Demo Mode is seeded content, never generated
  (`demo-mode-labeling`).
- The whole loop works with no network AI at all.
- Output quality is the user's AI's responsibility; SousMeow's responsibility is
  the review scaffolding.

## Current status

Active and load-bearing. Reversing it would redefine the product; do not add a
model API call.
