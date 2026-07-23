---
id: initialize-in-a-repository
type: functionality
title: Initialize VibeKB in a repository
area: integration
summary: A documented, project-agnostic process (INITIALIZE.md plus a paste-ready prompt) for an agent to build a fresh, honest `.vibekb/` model of another application read-only, then render it — the same process VibeKB used on itself, now started from an installed, scaffolded workspace.
status: partial
verification: reported-by-developer
user_facing: true
trigger: A developer runs `vibekb install` to prepare the repo, then runs the integration prompt in their coding agent.
updated: 2026-07-23
tags: [integration, onboarding, process]
files: [INITIALIZE.md, prompts/INTEGRATE_VIBEKB.md, index.php]
reads: []
writes: []
depends_on: [install-into-a-repository, load-living-model, validate-model, generate-static-snapshot]
related_memory: [assumption:agents-follow-session-workflow, change:homepage-install-fast-start, change:homepage-compatibility-section, change:homepage-native-installer-copy, change:homepage-voice-pass, change:release-binaries-pipeline, change:homepage-releases-install-copy, change:homepage-drop-no-go-php-claims, change:website-curl-installer, change:homepage-windows-install-copy]
---

## In one sentence

Initialization is a repeatable agent workflow — inventory the target read-only,
trace functionality from source, record honest verification states, add only
source-grounded diagrams, validate, and generate — that produces a model of any
app without modifying it.

## Current behavior

`INITIALIZE.md` defines the step-by-step process and
`prompts/INTEGRATE_VIBEKB.md` is a project-agnostic prompt that drives it. The
workspace it starts from is now prepared by the native installer
(`install-into-a-repository`) rather than a manual copy: `vibekb install`
copies the runtime and scaffolds an empty, valid `.vibekb/` (no PHP required to
install). The *building* of the model is still a **documented process**, not
executable code — the quality depends on the agent following it. It is
demonstrated by the bundled example models under `examples/` (the SousMeow model
and the StopPR field-test audit) and, most directly, by this repository —
VibeKB's own model was produced by this workflow.

## Implementation map

- `INITIALIZE.md` — the 17-step process.
- `prompts/INTEGRATE_VIBEKB.md` — the paste-ready driver prompt.
- `examples/` — worked results (see the field-test audit).

## Current state

- **Status:** partial — the workspace scaffolding is now automated by the
  installer and `bootstrap` (see `install-into-a-repository`,
  `bootstrap-workspace`), but *building* the model from source remains an
  agent-run process, not executable code; the agent authors the records directly.
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
