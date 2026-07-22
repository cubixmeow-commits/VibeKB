---
id: honest-provenance-no-auto-update
type: decision
title: Provenance is objective and VibeKB never claims to auto-update
summary: Every rendering states the source commit analyzed, when it was generated, and that it does not update itself; detection is separated from interpretation, and `updates_automatically` stays false.
status: accepted
verification: verified-from-source
updated: 2026-07-22
functionality: [show-provenance, validate-model, detect-drift]
files: [guide/lib/Provenance.php, .vibekb/manifest.json]
tags: [honesty, provenance, product]
---

## Context

The user wants VibeKB to "run continuously." The dishonest way to satisfy that is
to imply a file watcher understands code changes. It does not — understanding a
change is analysis work.

## Decision

Provenance uses objective labels only (source commit analyzed, analysis
generated, verification scope, last verified) and always states that the output
does not auto-update. `updates_automatically` is `false` unless a real, verified
update mechanism exists. The maintenance CLI is explicit about the boundary
between *detected* (mechanical: git diff, path existence, render diff) and
*interpreted* (an agent deciding what a change means).

## Alternatives considered

- **A single boolean "up to date" flag** — rejected: it cannot express "code
  changed but no one has reconciled the model yet," which is the common and
  important state.
- **Claiming automatic semantic updates** — rejected: false. VibeKB is
  agent-maintained; the manifest says so (`model_maintenance: agent-maintained`).

## Reason

VibeKB's entire value is trust. A single overstated freshness claim would poison
it.

## Consequences

- The drift check reports facts and never silently "fixes" the model.
- Verification states must reflect real evidence (see the warning of the same
  name).
- The provenance panel never renders an undefined or flattering label.
