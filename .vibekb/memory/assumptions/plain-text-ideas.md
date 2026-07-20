---
id: plain-text-ideas
type: assumption
title: Ideas are plain text, no attachments
summary: An idea is a title plus free-text notes. No images, files, or rich formatting are stored.
status: active
confidence: high
verification: verified-from-source
created: 2026-01-20
updated: 2026-07-05
functionality: [create-idea, view-idea]
invalidated_by: A requirement to attach mockups, screenshots, or files to an idea.
next_check: Revisit if the operator asks to attach anything to an idea.
tags: [scope, data]
---

## Claim

Every idea is fully represented by plain text: a title and notes. There is no
upload handling and none is planned.

## Confidence

High — verified by reading the create path and schema; there is no file-handling
code.

## Affected functionality

`create-idea` (no file input), `view-idea` (renders text only).

## What would invalidate it

Any need to attach media to an idea. That would introduce upload handling,
storage location decisions, and new failure modes.

## Next verification action

None needed unless attachments are requested.
