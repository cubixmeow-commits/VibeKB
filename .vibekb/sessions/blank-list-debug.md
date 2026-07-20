---
title: Session — Blank ideas list after deploy
summary: Debugging session that produced the ordered checklist still used in the guide.
date: 2026-03-22
order: 3
---

## Symptom

Ideas list empty in production; worked locally.

## Discovery path (now permanent)

1. Does `data/ideas.sqlite` exist on the server?
2. Can PHP read the file (permissions)?
3. Does the query return rows (schema match)?

## Root cause

Database file not deployed; `.cpanel.yml` excludes `*.sqlite` by design.

## Lesson captured

Deploy excludes are not bugs—they are decisions. Document them before the next incident.
