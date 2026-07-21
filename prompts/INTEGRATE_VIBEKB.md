# Prompt — Integrate VibeKB into this repository

Paste this to a coding agent (Claude Code, Cursor, etc.) working inside the
target repository. It is **project-agnostic**: it does not assume any language,
framework, or product. It produces a VibeKB living software model plus the
output you choose (PHP guide, static `/docs`, or both).

---

You are integrating **VibeKB** into this repository. VibeKB explains **what this
software currently does** — organized around functionality (behaviours), not
file dumps — and keeps that explanation honest about what is intended,
implemented, and verified.

**Read first, in this order:** `PRODUCT.md`, `SCHEMA.md`, `CLAUDE.md` /
`AGENTS.md`, `INITIALIZE.md`, and this repository's own agent instructions
(`CLAUDE.md`, `AGENTS.md`, `.cursor/rules`, `README`). Follow the 17-step
workflow in `INITIALIZE.md`.

**Hard rules — do not violate:**

- **Do not modify the application's code.** Read it; do not change it. VibeKB
  lives only in `.vibekb/`, `guide/`, `tools/`, `/docs`, and VibeKB docs.
- **Do not invent functionality.** Trace behaviour from source. The README is
  **not** sufficient proof — verify against the actual code.
- **Do not claim manual or test verification you did not perform.** Use
  `inferred-from-source` when you have strong code evidence but did not execute
  it; `not-verified` when runtime behaviour is unknown.
- **Do not expose secrets.** Never copy values from `.env*`, key files, or CI
  config into `.vibekb/` or the guide. Reference that a secret exists, never
  its value.
- **Do not imply the guide updates itself.** It is a snapshot of the commit you
  analysed. Record that commit; set `updates_automatically: false`.
- **Do not turn VibeKB into a generic documentation generator, repository
  browser, changelog, or AI activity log.** The subject is current software
  functionality.

**Do:**

1. Inventory the repo (language, entry points, run/deploy, storage, config,
   external services) **without editing app code**.
2. Identify **functional areas**, then trace **individual functionality
   records** from source, each with an honest `status` and `verification`.
3. Curate **files that matter** (evidence-based safety levels), **map data and
   storage**, and **list external services** and their failure impact.
4. Capture **active warnings and placeholders** — real landmines — each with
   affected functionality, severity, and a safe next action.
5. Add a **small, source-grounded diagram set** under `.vibekb/diagrams/`
   (records + repository-owned SVGs with accessible `<title>`/`<desc>`; label
   inferred paths). Do not add diagrams you cannot ground in source.
6. Write `work/current.md` and `work/handoff.md` with an explicit next action.
7. Set the `provenance` block in `manifest.json`: source repository, branch,
   **commit analyzed**, verification scope, last verified.
8. **Validate:** run `php tools/validate.php` and resolve every error.
9. **Generate output:**
   - Dynamic guide: deploy `guide/` (PHP 8.2, reads `.vibekb/` live), or
   - Static snapshot: `php tools/generate-static.php` → `/docs` for GitHub
     Pages, or both.
10. **Verify:** dynamic views load; static `/docs/index.html` loads and works
    under a repository subpath; links and asset paths resolve; SVGs are valid
    XML with title/desc; search entries resolve; **no app code changed**.

**Deliver a short report:** functional areas and record counts (state the
unit — "N functionality records across M functional areas"), the verification
mix, active warnings, the commit analysed, which output mode you generated, and
the single most useful next action for the next agent.
