---
title: No file uploads
summary: Ideas are text fields only. The application does not accept image or document uploads and has no storage path for user files.
status: accepted
date: 2026-03-20
updated: 2026-07-08
order: 4
---

## Decision

Reject file upload features for now. Idea content is text stored in SQLite.

## Why

- Uploads introduce validation, malware, disk quota, and permission issues on shared hosting.
- The core job—capturing SaaS ideas—does not require attachments.
- Skipping uploads keeps the security surface smaller while auth is absent.

## Consequences

- Users cannot attach mockups or PDFs inside the app.
- Any future upload feature needs a storage layout, size limits, and auth or access policy first.
