---
id: project-identity
type: project
title: SousMeow
summary: A guided AI cooking companion that packages proven workflows as Cookbooks of step-by-step Recipes — and never calls an AI itself.
one_liner: Run step-by-step AI workflows using the AI subscription you already have, with human review at every step.
intended_users: Makers and independent creators who use a chat AI (ChatGPT, Claude, Gemini, etc.) and want reliable, reviewed output instead of a black box.
primary_outcome: A finished, human-reviewed deliverable — exported as a clean Project Kit (Markdown files, an offline HTML reader, and a manifest).
stack_language: PHP 8 (custom MVC, no framework)
stack_database: SQLite (local dev) and MySQL (production) — same schema, two dialects
stack_hosting: Hostinger shared hosting; document root is public/, app/config/storage sit above it
stack_frontend: Server-rendered PHP views, progressive-enhancement JS, self-hosted assets
source_repository: cubixmeow-commits/dev-portfolio-v2 (projects/sousmeow)
verification: verified-from-source
updated: 2026-07-20
---

## What the software is

SousMeow is a web app that turns a proven workflow into a **Cookbook** made of
ordered **Recipes**. You stock a **Pantry** with facts about your project; each
Recipe turns those facts into a precise prompt; you run that prompt in the AI
you already pay for; you paste the answer back; you confirm human **Quality
Checks**; and you approve the result. When every Recipe is approved, the whole
project exports as a **Project Kit**.

The defining architectural fact: **SousMeow deliberately never calls an AI
itself.** There are no API keys and no token billing. The product is structure,
review discipline, immutable version history, and a finished deliverable —
never a black box.

> Verified from source: `README.md`, `app/routes.php`, `app/Controllers/RunnerController.php`,
> `app/Services/PromptBuilder.php`, `app/Services/ProjectKit.php`.

## Who uses it

Independent makers who use a chat AI and want dependable, reviewed output. An
account is required to run a Cookbook (email verification gates the write
actions). Admins exist only through the CLI seed script.

## Current scope

- Discovery: a marketing home, a searchable marketplace, categories, and
  curated collections over a catalog of Cookbooks.
- The Runner: the core create → pantry → run → review → approve → export loop.
- Accounts: registration, email verification, login, account settings, data
  export, and account deletion.
- A portfolio **Demo Mode / simulation** that can populate hundreds of
  simulated creators and a public activity dashboard — all clearly labelled.

## Explicit non-goals (v1, from the source)

- No AI API calls, no API keys, no token markup.
- No payment SDK or checkout (marketplace previews are honest "coming soon").
- No Node build step, no Composer, no Docker, no background workers.
- Cookbooks are seeded from versioned files, not authored through the web UI.

## About this VibeKB model

SousMeow is the **real application** VibeKB is explaining here. It is not
bundled into VibeKB. Everything below was derived by reading the SousMeow
source read-only. File paths are relative to `projects/sousmeow/` in the
[source repository](https://github.com/cubixmeow-commits/dev-portfolio-v2).
