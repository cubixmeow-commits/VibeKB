---
id: low-volume-single-writer
type: assumption
title: Low volume, one writer at a time
summary: The app assumes a few hundred ideas at most and only one person writing, so no pagination, caching, or locking is needed.
status: active
confidence: medium
verification: reported-by-developer
created: 2026-02-10
updated: 2026-07-01
functionality: [browse-ideas, export-ideas]
invalidated_by: The list growing into thousands of ideas, or a second concurrent writer.
next_check: Measure list-page render time once the database exceeds ~500 ideas.
tags: [performance, scope]
---

## Claim

The operator will hold at most a few hundred ideas and will never have two
writers at once.

## Confidence

Medium — this is the operator's expectation, not something measured. It has
**not** been verified against a large database.

## Affected functionality

`browse-ideas` renders the whole list with no pagination; `export-ideas`
streams everything in one request. Both rely on this assumption holding.

## What would invalidate it

Thousands of ideas (list render and export get slow) or concurrent writers
(SQLite write locking becomes visible).

## Next verification action

Load-test the list and export once the database passes ~500 ideas.
