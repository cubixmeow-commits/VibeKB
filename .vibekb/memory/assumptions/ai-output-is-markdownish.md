---
id: ai-output-is-markdownish
type: assumption
title: Pasted AI output is Markdown-ish
summary: Quality-check evidence parsing and the export reader assume responses use Markdown-like headings and structure; free-form prose falls back to manual review.
status: active
confidence: medium
verification: inferred-from-source
updated: 2026-07-16
functionality: [review-quality-checks, export-project-kit]
invalidated_by: Cookbooks whose prompts ask for output that is not heading-structured, making contract parsing consistently unhelpful.
next_check: Sample real pasted responses for a few Cookbooks and confirm the output-contract sections parse.
tags: [parsing, review]
---

## Claim

`ResponseParser` and `SafeText` assume responses are Markdown-ish (headings and
sections), so the output contract can locate evidence and the kit reader can
format the text.

## Confidence

Medium — the prompts request structured output and each runnable Recipe ships a
realistic `example_response`, but the parser internals were read only at the
call boundary in this pass.

## Affected functionality

`review-quality-checks` (evidence) and `export-project-kit` (kit.html
rendering). Both degrade gracefully: no contract → manual review; unparsed text
still renders.

## What would invalidate it

Widespread use of non-structured output where contract parsing never finds
sections.

## Next verification action

Trace `ResponseParser`/`OutputContract` and diff against a few seeded
`example_response` values.
