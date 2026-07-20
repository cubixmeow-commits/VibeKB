---
title: Read This First
summary: SaaS Idea Manager is a single-user PHP and SQLite app for capturing SaaS ideas. This edition explains what exists today, what is deliberately missing, and how to stay oriented as the code grows.
updated: 2026-07-18
order: 1
---

## What this project is

SaaS Idea Manager is a small PHP application that stores multiple SaaS product ideas in a local SQLite database. It is meant for one operator working on a cPanel host. You can create ideas, edit them, review them later, and keep a lightweight record of what you might build.

It is not a multi-tenant product. It does not authenticate users. It does not accept file uploads. Those absences are intentional for Version 1 of the application, not unfinished placeholders waiting to be filled in silently.

## Why this edition exists

AI coding agents can add features faster than a human can absorb the resulting architecture. The biggest risk in this repository is not a missing button. It is losing the ability to explain how the pieces fit together.

This publication is the maintained explanation. Prefer it over reconstructing intent from scattered commits.

## How to use it

1. Read **How the Project Works** for the request and data flow.
2. Skim **Current Risks** before asking an agent to add accounts, sharing, or uploads.
3. Use **Project Map** when you need the right file path.
4. Check **Where Bugs Usually Start** before deep debugging.

## What this edition will not do

It will not replace reading code when you are changing a specific function. It will tell you which assumptions and decisions make that change safe or dangerous.
