---
id: verification-must-reflect-evidence
type: warning
title: A verification state must reflect real evidence
summary: A record is not "verified" because an AI edited it or a file exists; only claim `verified-*` for behaviour you traced, tested, or exercised, and mark the rest honestly.
severity: high
status: active
verification: verified-from-source
updated: 2026-07-22
functionality: [validate-model, show-provenance, validate-diagram-topology]
files: [guide/lib/helpers.php, SCHEMA.md]
tags: [honesty, verification, gotcha]
---

## What can go wrong

An agent regenerates a record and, because it "looks right," sets
`verified-from-source` without actually tracing the code. The model now overstates
its own confidence — and the guide presents that as fact.

## Cause

Editing a record is easy; verifying behaviour is work. The verification
vocabulary exists precisely to keep those separate: `verified-by-test`,
`verified-manually`, `verified-from-source`, `inferred-from-source`,
`reported-by-developer`, `not-verified`.

## What not to do

- Do not upgrade a verification state you did not confirm.
- Do not mark an inferred diagram edge verified to make the line solid.
- Do not claim manual/test verification you did not perform.

## Safe procedure

Trace or exercise the behaviour, then set the state that matches the evidence you
actually have. When in doubt, use `inferred-from-source` (strong code evidence,
not executed) or `not-verified` (runtime unknown) and say why in the body.

## Note

The validator enforces that the *value* is in the vocabulary; it cannot enforce
that the value is *honest*. That is the agent's responsibility, and it is the
one that matters most.
