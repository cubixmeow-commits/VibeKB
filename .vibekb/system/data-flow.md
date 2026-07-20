---
id: system-data-flow
type: system
title: How data flows
summary: Where an idea's data comes from, how it moves through the app, and where it ends up.
updated: 2026-07-16
---

## From keystroke to stored row

```
Form input  →  IdeaService (validate + normalise)  →  IdeaRepository (INSERT)  →  ideas table
```

## From stored row to screen

```
ideas table  →  IdeaRepository (SELECT)  →  controller  →  template  →  HTML
```

## What is trusted where

- Input from the browser is **never trusted** until `IdeaService` has
  validated and normalised it.
- Output to the browser is **always escaped** in templates.
- SQL parameters are **always bound**, never interpolated.

## The alignment rule

The set of fields written on create must match the set of fields read on list
and detail. When they drift, ideas appear to "lose" fields — the exact failure
described in the `read-write-path-drift` warning and the `blank-list-ordering`
discovery.
