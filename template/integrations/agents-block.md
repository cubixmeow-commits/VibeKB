## VibeKB

This repository uses **VibeKB** to keep an honest, living explanation of what the
software currently does. VibeKB's knowledge base and its tooling live entirely
under [`.vibekb/`](./.vibekb/) — nothing else in this file is managed by VibeKB.

- **Orient:** `vibekb status` (or `php .vibekb/runtime/tools/vibekb.php status`)
- **Operating rules & lifecycle:** [`.vibekb/reference/WORKFLOW.md`](./.vibekb/reference/WORKFLOW.md)
- **Build or update the model:** follow [`.vibekb/prompts/INTEGRATE_VIBEKB.md`](./.vibekb/prompts/INTEGRATE_VIBEKB.md)
- **Before finishing a change:** `vibekb check` must be clean.

VibeKB owns only `.vibekb/` and this marked block. Everything outside the block
is yours; VibeKB never edits it.
