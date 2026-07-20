---
id: project-identity
type: project
title: SaaS Idea Manager
summary: A single-user PHP + SQLite web app for capturing, reviewing, and prioritising SaaS product ideas.
one_liner: Capture a SaaS idea in seconds and keep every idea in one prioritised list.
intended_users: One builder (the operator) exploring product ideas on their own machine or a personal cPanel site.
primary_outcome: Nothing you thought of gets lost, and you can see which ideas matter most at a glance.
stack_language: PHP 8.2
stack_database: SQLite
stack_hosting: cPanel shared hosting (subfolder deploy)
stack_frontend: Server-rendered PHP templates, progressive enhancement
updated: 2026-07-18
---

## What the software is

The SaaS Idea Manager is a small web application for one person. It lets the
operator write down a product idea — a title, some notes, and a status — and
keeps every idea in a single list ordered by priority. There are no accounts,
no teams, and no cloud services. It runs as plain PHP against a local SQLite
file.

## Who uses it

A single builder. The app assumes one trusted operator and does not implement
authentication or per-user ownership. See the `single-user-no-auth`
constraint.

## Current scope

- Create, view, browse, and re-prioritise ideas.
- Change an idea's status through its lifecycle.
- Export the full list as CSV.

## Explicit non-goals

- Multiple users or authentication.
- File uploads or rich media.
- Real-time collaboration or an external API.
- A JavaScript build step or SPA front end.
