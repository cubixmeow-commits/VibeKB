---
id: cookbook-count-drift
type: discovery
title: The README's Cookbook count is stale
summary: README says "Twenty-two executable Cookbooks"; the seed source actually contains 31 executable + 2 preview (33 total).
status: open
verification: verified-from-source
updated: 2026-07-20
functionality: [browse-marketplace, seed-and-sync-content]
files: [README.md, database/seeds/cookbooks]
tags: [documentation, drift]
changed_model: true
---

## Discovery

`README.md` states "Twenty-two executable Cookbooks and two marketplace
previews," but the actual seed files tell a different story.

## Evidence

Counting `is_executable` in `database/seeds/cookbooks/*.php` at source commit
`c1617ab`: 31 files with `is_executable => true` and 2 with `false` (33 total).
A `docs/COOKBOOK_EXPANSION_ANALYSIS.md` file also exists, consistent with the
library having grown past the README's number.

## Affected functionality

`browse-marketplace` (what's listed) and `seed-and-sync-content` (what's
seeded). The counts on any VibeKB or product surface should come from the seed
source, not the README prose.

## Consequence

This VibeKB model reports **31 executable + 2 preview**, and notes the README as
stale.

## Action taken

Recorded here; `project/current-state.md` uses the source-derived counts.

## Did it change the software model?

Yes — the current-state count is sourced from the seeds, not the README.
