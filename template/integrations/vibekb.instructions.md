---
applyTo: "**"
---

# VibeKB — instructions for coding agents

This repository uses **VibeKB** to keep a living, honest explanation of what the
software currently does. VibeKB's knowledge base and tooling live entirely under
`.vibekb/`. This namespaced instructions file is owned by VibeKB; it does not
modify any other instructions in this repository.

- **Orient first:** run `vibekb status` (or
  `php .vibekb/runtime/tools/vibekb.php status`).
- **Canonical operating rules & lifecycle:** `.vibekb/reference/WORKFLOW.md`.
- **Build or update the model:** `.vibekb/prompts/INTEGRATE_VIBEKB.md`.
- **Gate before finishing:** `vibekb check` must be clean.

Be honest about intended vs implemented vs verified behaviour, and never imply
VibeKB updates itself — it is agent-maintained.
