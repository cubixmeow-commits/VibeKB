---
id: constraints
type: project
title: Boundaries VibeKB runs inside
summary: PHP 8.2 shared hosting, deployable in a subfolder, usable without JavaScript, without a database, without an external/AI API, and without a build step; all file access confined to the content root; output always escaped.
updated: 2026-07-22
---

## Runtime boundaries (do not cross)

- **PHP 8.2, shared hosting.** No framework, SPA, bundler, or SQL database.
- **Subfolder-safe.** Query-string routing means no rewrite rules are required;
  the static snapshot uses relative links so it works under a repository subpath.
- **Works without JavaScript.** JS only enhances; nothing essential is behind it.
- **No database, no external/AI API, no embeddings, no vector store, no
  background workers, no build step.** Any AI analysis is performed by the coding
  agent already working in the repository — never by a service VibeKB calls.
- **No network at render or generate time.**

## Safety boundaries

- **Confine file access to the content root.** Record ids are constrained to a
  safe character set; a crafted `?id=` can never escape `.vibekb/`.
- **Escape all output.** Untrusted content (record bodies, pasted text) is
  escaped before any allowlist formatting is applied.
- **Never fabricate provenance.** `updates_automatically` stays `false` unless a
  real, verified update mechanism exists. Source links are built only from
  recorded provenance and never invent line numbers.

## Product boundary

The primary subject is **software functionality**. Repository memory exists to
protect that explanation. Do not reinterpret VibeKB into a memory product, a
documentation generator, a code browser, or an AI activity log. See
[PRODUCT.md](../../PRODUCT.md).
