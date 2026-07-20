---
title: Do not auto-apply migrations in production dark
summary: Manual migrations exist so schema changes are visible. Silent auto-migrate on every request hides failures until data is wrong.
severity: medium
updated: 2026-07-15
order: 3
---

Avoid “migrate on boot” shortcuts that run unchecked SQL on every page load. If you introduce a runner, make it explicit, logged, and documented here.
