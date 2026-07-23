---
id: homepage-compatibility-section
type: change
title: Homepage Compatibility & Requirements under install
summary: Install step 3 is agent-agnostic (Cursor, Claude Code, Codex, Windsurf, and others). A new Compatibility & Requirements section under #install answers stack/agent/deploy questions with honest limits and inactive coming-soon badges.
status: implemented
verification: verified-from-source
updated: 2026-07-23
functionality: [install-into-a-repository, initialize-in-a-repository, deploy-and-stay-portable]
files: [index.php, assets/css/homepage.css]
tags: [homepage, compatibility, onboarding, copy, change]
---

## Before

Install step 3 and boundary copy named Cursor alone. Visitors had no immediate
answer to “will this work with my stack?” after the three install steps.

## After

- Step 3: **Ask your coding agent** — lists Cursor, Claude Code, Codex, Windsurf,
  and other capable agents; copy button says “Copy agent prompt.”
- New `#compatibility` section with four cards (install requirements, works-with
  stacks, named agents, deployment), a no-extra-infrastructure checklist, honest
  current requirements (PHP 8.2+, a coding agent, a developer to initialize), and
  inactive coming-soon badges.

## Honesty preserved

Stack badges mean an agent can model those codebases — VibeKB does not parse the
languages. Coming-soon items are labelled as not implemented. Installation still
does not automatically analyze repositories.
