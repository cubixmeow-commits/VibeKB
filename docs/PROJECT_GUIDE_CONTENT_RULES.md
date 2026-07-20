# Project Guide content rules

Relevance filter for what belongs in a VibeKB Project Guide.

## Include

Surface technical facts that change how someone safely works on the project, especially when they help with:

- Understanding the system’s purpose and shape
- Making a modification without breaking invariants
- Preventing risk (auth halfway, uploads, schema drift)
- Debugging common failures quickly
- Continuity between developers or coding agents
- Explaining architectural intent (“why it is simple”)

Prefer:

- Mental models
- Intent behind decisions
- Invariants (read/write alignment, ownership rules)
- Risks and consequences
- Debugging shortcuts and ordered checks
- Change-impact maps (“this affects…”)

## Exclude

Do not include information merely because it can be extracted.

Avoid:

- Every file, folder, or function
- Full line-by-line schemas
- Obvious framework or language behavior
- Exhaustive dependency inventories
- Stale implementation trivia
- Repeated paragraphs copied into every chapter
- Automated repository exhaust

If a fact is immediately obvious from reading the code and does not change decision-making, leave it out of the guide.

## Depth discipline

Do not write three unrelated essays for Understand / Work on it / Reference.

- State the shared fact once in structured form.
- Show the plain-language version by default.
- Reveal operational consequences behind an explicit developer control.
- Link to the full article in the technical reference when depth is needed.

## Source of truth

Guide chapters should be grounded in `.vibekb/` knowledge files (and the real application they describe).

Do not invent:

- Features that do not exist
- Failure modes not supported by existing debugging notes
- Security properties the app does not provide

When the application changes meaningfully, update the knowledge files and the affected chapters in the same change set.
