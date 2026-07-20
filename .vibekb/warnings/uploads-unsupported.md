---
title: Uploads are unsupported
summary: There is no safe upload path. Do not wire multipart forms to arbitrary directories on the server.
severity: high
updated: 2026-07-08
order: 2
---

File uploads are out of scope. Adding them without size limits, type checks, and an access policy is a hosting incident waiting to happen—especially while authentication is absent.
