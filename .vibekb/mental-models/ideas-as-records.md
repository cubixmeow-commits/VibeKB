---
title: Ideas as first-class records
summary: An idea is a durable row with its own lifecycle, not a note stuck in a markdown file or a temporary form draft.
updated: 2026-07-10
order: 1
---

## Model

Treat each SaaS idea as a first-class record: identity, title, body, status, timestamps. Features should orbit that record rather than inventing parallel stores.

## Implications

- Deleting an idea is a deliberate data event.
- Status changes belong on the record, not only in UI labels.
- Search or tagging later should attach to idea identity, not scrape free text alone.
