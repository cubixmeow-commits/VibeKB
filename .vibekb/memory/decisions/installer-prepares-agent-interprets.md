---
id: installer-prepares-agent-interprets
type: decision
title: The installer prepares the workspace; the agent interprets the software
summary: install.php and bootstrap only scaffold an empty, valid VibeKB workspace and install the runtime — they never analyse, understand, or document the target application. Building the model is left to an AI agent following the integration prompt.
status: accepted
verification: verified-from-source
updated: 2026-07-22
functionality: [install-into-a-repository, bootstrap-workspace]
files: [internal/installer/installer.go, tools/lib/Starter.php, install.php]
tags: [installer, honesty, boundary]
---

## Context

The installer removes the friction of adopting VibeKB. The tempting next step is
to have it also inspect the repository and pre-fill functionality — but that would
fabricate understanding a script cannot honestly have, exactly the drift VibeKB
exists to prevent.

## Decision

The installer (and `bootstrap`) do only mechanical preparation: copy the runtime,
create directories, and write empty starter placeholders. Every starter record is
explicitly a placeholder that tells an agent what to write; none claims the
software does anything. Provenance is left blank because no commit was analysed.
An AI agent builds the model afterwards via `prompts/INTEGRATE_VIBEKB.md`.

## Alternatives considered

- **Auto-analyse the repo during install** — rejected: it would produce
  unverified, fabricated functionality and undermine VibeKB's honesty guarantees.
- **Ship a pre-filled example model** — rejected: it would describe some other
  app, not the target, and invite confusion with the real model.

## Reason

VibeKB's core promise is an honest explanation of what software actually does.
A script cannot trace behaviour; only analysis can. Keeping that line bright is
more valuable than a flashier one-command "model."

## Consequences

- A fresh install has zero functionality records — valid but empty, by design.
- The verification step reports the empty model as OK, not as a failure.
- The hand-off to the agent is explicit in the installer's final output.
