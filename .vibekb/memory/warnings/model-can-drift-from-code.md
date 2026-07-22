---
id: model-can-drift-from-code
type: warning
title: The model drifts from the code unless an agent reconciles it
summary: VibeKB detects that files changed but cannot interpret what a change means; if an agent edits code without updating the affected records, the model silently goes stale.
severity: high
status: active
verification: verified-from-source
updated: 2026-07-22
functionality: [detect-drift, find-affected-functionality]
files: [tools/vibekb.php, guide/lib/Content.php]
tags: [drift, gotcha, maintenance]
---

## What can go wrong

An agent changes `guide/lib/Content.php` behaviour but leaves `load-living-model`
and `validate-model` describing the old behaviour. The model now claims something
the code no longer does — the exact failure VibeKB exists to prevent.

## Cause

Reconciling a code change into the model is interpretation work; it cannot be
fully automated. The tooling can detect that a file changed and which records
reference it, but not what the change *means*.

## What not to do

Do not treat a green `vibekb check` as proof the model is correct — it proves
nothing is *mechanically* inconsistent (no missing files, valid structure), not
that the prose still matches behaviour. Do not skip step 5 of the lifecycle
(update the affected records) because the code "already works."

## Safe procedure

1. Run `php tools/vibekb.php affected --since <base>` to see likely-affected
   records.
2. For each, re-read the code and update the record's behaviour and verification
   state honestly.
3. Update memory, diagrams, provenance, and the handoff.
4. Run `php tools/vibekb.php check` and regenerate `/docs`.
