---
id: php82-cpanel-subfolder
type: constraint
title: PHP 8.2 on cPanel, subfolder deploy, no Node
summary: The app must run on ordinary cPanel shared hosting with PHP 8.2, deploy into a subfolder, and require no Node or build step.
status: active
verification: reported-by-developer
created: 2026-01-14
updated: 2026-07-10
functionality: [initialize-database, browse-ideas]
tags: [hosting, deployment]
---

## Constraint

- Runs on PHP 8.2 on shared cPanel hosting.
- Must work when served from a subfolder, not only the web root.
- No Node.js, no bundler, no build step in production.

## Source

The operator's hosting is a standard cPanel account with Git Version Control
deploys and no Node runtime.

## Affected functionality

Every page. In particular, links must be relative (subfolder-safe) and no
feature may depend on a compiled asset.

## Consequences

- All front-end enhancement is progressive; the app works without JavaScript.
- Deployment is a file sync, not a build.

## Still active?

Yes. This constraint is unlikely to change and shapes every implementation
choice.
