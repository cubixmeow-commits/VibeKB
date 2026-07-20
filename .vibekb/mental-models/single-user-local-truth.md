---
title: Single-user local truth
summary: The SQLite file on the deployed host is the source of truth. There is no sync layer and no per-user partition.
updated: 2026-07-10
order: 2
---

## Model

One operator. One database file. One deployment. Truth is local to that host.

## Implications

- Do not design features that assume eventual consistency across devices.
- Backups matter more than replication.
- If two people need isolation, you no longer have this model—and ownership becomes mandatory.
