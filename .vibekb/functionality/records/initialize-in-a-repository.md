---
id: initialize-in-a-repository
type: functionality
title: Initialize VibeKB in a repository
area: integration
summary: A documented, project-agnostic process (INITIALIZE.md plus a paste-ready prompt) for an agent to build a fresh, honest `.vibekb/` model of another application read-only, then render it — the same process VibeKB used on itself.
status: partial
verification: reported-by-developer
user_facing: true
trigger: An operator copies the VibeKB app + docs into a target repo and runs the integration prompt.
updated: 2026-07-22
tags: [integration, onboarding, process]
files: [INITIALIZE.md, prompts/INTEGRATE_VIBEKB.md]
reads: []
writes: []
depends_on: [load-living-model, validate-model, generate-static-snapshot]
related_memory: [assumption:agents-follow-session-workflow]
---

## In one sentence

Initialization is a repeatable agent workflow — inventory the target read-only,
trace functionality from source, record honest verification states, add only
source-grounded diagrams, validate, and generate — that produces a model of any
app without modifying it.

## Current behavior

`INITIALIZE.md` defines the step-by-step process and
`prompts/INTEGRATE_VIBEKB.md` is a project-agnostic prompt that drives it. This is
a **documented process**, not executable code: the quality depends on the agent
following it. It is demonstrated by the bundled example models under `examples/`
(the SousMeow model and the StopPR field-test audit) and, most directly, by this
repository — VibeKB's own model was produced by this workflow.

## Implementation map

- `INITIALIZE.md` — the 17-step process.
- `prompts/INTEGRATE_VIBEKB.md` — the paste-ready driver prompt.
- `examples/` — worked results (see the field-test audit).

## Current state

- **Status:** partial — the process is complete and demonstrated, but there is no
  scaffolding command that generates an empty `.vibekb/` skeleton yet; the agent
  authors records directly.
- **Verification:** reported-by-developer — the process is validated by its
  outputs (the example models validate), not by an automated end-to-end test.

## Use caution

Never modify the target application's code to initialize VibeKB. Never claim
verification you did not perform. The README of a target repo is not proof of
behaviour.

## Why it works this way

Keeping initialization a documented, agent-run process (rather than a code
generator) matches the honest-automation boundary: understanding an unfamiliar
codebase is analysis work an agent does, not something a script can fake.
