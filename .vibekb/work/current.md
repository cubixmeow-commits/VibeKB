---
id: current-work
type: work
title: Homepage compatibility section and agent-agnostic install
objective: Make the homepage install steps agent-agnostic (not Cursor-only) and add a Compatibility & Requirements section that answers “Will this work with my stack?” without false claims.
summary: In progress — install copy generalized; compatibility cards pending verification against PRODUCT.md / DEPLOYMENT.md / installer constraints.
requested_by: cubix.meow@gmail.com
status: in-progress
verification_state: not-verified
updated: 2026-07-23
affected_functionality: [install-into-a-repository, initialize-in-a-repository, deploy-and-stay-portable]
expected_files: [index.php, assets/css/homepage.css]
data_impact: None — marketing homepage only.
risks: [Over-claiming language support or agent compatibility; implying VibeKB parses languages directly; presenting roadmap items as shipped.]
---

## Requested outcome

1. Install step 3 and surrounding copy name coding agents generally (Cursor,
   Claude Code, Codex, Windsurf, and others), not Cursor alone.
2. A Compatibility & Requirements section immediately under `#install` with
   four compact cards, a “no extra infrastructure” checklist, honest current
   requirements, and inactive “coming soon” badges.

## Verification plan

- Cross-check every claim against PRODUCT.md, INSTALLER.md, DEPLOYMENT.md, and
  technical guardrails (PHP 8.2, no Composer/DB/AI API, agent-maintained).
- Render homepage; confirm section order and mobile layout.
- `php -l index.php`, `php tools/vibekb.php check`, topology, generate.
