---
id: system-data-flow
type: system
title: How data flows
summary: Pantry facts flow into prompts; pasted answers become immutable artifact versions; confirmed checks gate approval; approved artifacts chain into later prompts and the export.
updated: 2026-07-16
verification: verified-from-source
---

## From facts to finished kit

```
Pantry values ─► PromptBuilder ─► prompt (copied) ─► [your AI] ─► pasted text
                                                                      │
                                          cleanContent (sanitise) ────┘
                                                       │
                                       artifact_versions (immutable, append-only)
                                                       │
                            ResponseParser ─► Quality Check evidence ─► artifact_checks
                                                       │  (all confirmed)
                                                    approve ─► approved version
                                                       │
                     chains into later prompts  ◄──────┤
                                                       ▼
                                              ProjectKit ─► export zip
```

## What is trusted where

- **Pantry input** is validated per type before storage (`savePantry`).
- **Pasted AI output** is untrusted: sanitised on store (`cleanContent`) and
  escaped on render (`SafeText`) end to end.
- **SQL parameters** are always bound (one PDO path); never interpolated.
- **Ownership** is enforced at the data layer; ids are never leaked.

## The alignment rule

The fields written for the Pantry and Artifacts must match the fields read by
`PromptBuilder`, the Runner, and `ProjectKit`. When they drift, prompts get
`[missing:]` values or the export omits content — the failure the
`read-write-path-coupling` warning describes.
