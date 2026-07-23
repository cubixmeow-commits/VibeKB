---
id: handoff
type: handoff
title: Current handoff
summary: Homepage install is agent-agnostic and followed by Compatibility & Requirements. Next: keep homepage claims aligned with INSTALLER.md / PRODUCT.md / DEPLOYMENT.md.
updated: 2026-07-23
verification_state: verified-from-source
---

## Completed this change

- Install step 3 generalized: **Ask your coding agent** (Cursor, Claude Code,
  Codex, Windsurf, and others); copy control is “Copy agent prompt.”
- Boundary copy: installer prepares; coding agent understands.
- New `#compatibility` section: four cards, no-extra-infrastructure checklist,
  honest current requirements, inactive coming-soon badges.
- Nav link: Compatibility.
- Model reconciled: change memory, important-files, functionality links, handoff.

## Verification completed

- Claims checked against PRODUCT.md (agents/audience, no DB/AI API), INSTALLER.md
  (PHP 8.2+, no Composer/network), DEPLOYMENT.md (cPanel / GitHub Pages / static).
- Rendered homepage section order: problem → install → compatibility →
  understanding → proof.
- Confirmed honesty lines: agent interprets source; no auto-analyze on install;
  coming soon labelled not implemented.
- `php -l index.php`; `php tools/vibekb.php check`; `php tools/test-topology.php`;
  `php tools/vibekb.php generate`.

## Exact next recommended action

`php tools/vibekb.php status` before the next change. If install commands,
supported agents, or deploy modes change, update `#install` and `#compatibility`
in the same commit as the docs they mirror.
