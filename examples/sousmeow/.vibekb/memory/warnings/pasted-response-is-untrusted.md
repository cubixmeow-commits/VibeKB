---
id: pasted-response-is-untrusted
type: warning
title: Pasted AI responses are untrusted input end to end
summary: Response content is sanitised on store and escaped-before-formatting on render; relaxing either — especially in the export reader — reintroduces stored XSS.
severity: high
status: active
verification: verified-from-source
updated: 2026-07-16
functionality: [paste-response, review-quality-checks, export-project-kit]
files: [app/Controllers/RunnerController.php, app/Services/SafeText.php, app/Services/ProjectKit.php]
tags: [security, xss, gotcha]
---

## Affected functionality

Anywhere pasted content is stored or rendered: the Runner review, and the
exported kit.html.

## What can go wrong

Pasted content can contain HTML/script. If it were rendered without escaping —
in the review UI or, easily overlooked, in the self-contained `kit.html` reader
— it becomes stored XSS.

## Cause

The content originates outside the app (the user's AI) and is displayed as rich
text.

## What not to do

Do not render pasted content with raw output. Do not "improve" `SafeText` by
formatting before escaping.

## Safe procedure

- On store: `cleanContent()` normalises newlines, strips control characters, and
  bounds length.
- On render: `SafeText::render()` escapes first, then applies a small Markdown
  allowlist to the escaped text. `ProjectKit` uses the same path for kit.html.
- Keep the CSP `script-src 'self'` as defence in depth.

## Verification steps

Paste content containing `<script>` and an `onerror` image; confirm it is inert
in the review UI and in the exported kit.html.
