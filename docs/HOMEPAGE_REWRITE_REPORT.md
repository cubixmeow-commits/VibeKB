# Homepage rewrite report

What changed in `index.php` (and why) during the positioning / information-
architecture / copy pass. Companion to `docs/HOMEPAGE_COPY_AUDIT.md`. This is a
process artifact; the static generator leaves `docs/*.md` untouched.

## Sections removed (as standalone sections)

Eleven sections became seven. These were removed as dedicated sections; their
substance was folded into the surviving narrative rather than deleted outright:

- **"Depths" (Understand / Work on it / Reference).** Its point — the same model
  read at the depth you need — is now carried by §6 ("the generated guide is a
  presentation layer") without a separate widget.
- **"Every functionality page answers…" (six-question filter).** Folded into §3
  as a single sentence naming what a record answers (what it does, step by step,
  which files, what data, what could go wrong, what's safe to change).
- **"Who / when" audience compare grid.** Audience is now stated directly in the
  §2 problem copy (solo builders, inheritors, the next human or agent).
- **Seven-principle manifesto.** Reduced to **three trust principles** in §6
  (functionality first · evidence and uncertainty stay visible · the model
  changes with the software), as static list items rather than an interactive
  carousel.

## Sections merged

- **Old §2 "questions" + §3 "four things" + §7 "record answers" → new §3 "What
  VibeKB adds to the repository."** One four-part model: Current functionality ·
  How it works · Active AI work · Repository memory. The overlapping "what does
  it do / how / what is AI changing" blocks that appeared three times are now
  stated once.
- **Old §5 depths + §9 architecture + §10 principles → new §6 "Why the
  repository-owned model matters."** Led by consequence ("Chat context expires.
  Repository context remains."), then the three principles, then the source-of-
  truth line, then the directory tree, then a collapsed implementation note.

## New seven-section order

1. Hero — category definition + live self-repo example card.
2. The problem — the "six files" spine + who it's for.
3. What VibeKB adds — the four-part model (interactive tabs retained).
4. Live proof — VibeKB explains itself (functionality carousel retained).
5. How it fits into AI-assisted development — the 5-stage workflow (retained) + "V1, honestly."
6. Why the repository-owned model matters — principles, source-of-truth, directory tree, implementation note.
7. Final CTA.

## Major copy changes

- **Hero.** Eyebrow now names the category: *Repository understanding for
  AI-assisted development.* Headline: *Understand the software AI helped you
  build.* Support copy makes the repository + AI-session context explicit.
- **`<title>` / meta description** rewritten to the repository-native product.
- **Problem section** built around the strongest existing line and the real
  multi-session workflow, ending on: *VibeKB keeps that understanding in the
  repository — instead of leaving it scattered across code, prompts, and old
  conversations.*
- **"Intended, implemented, and verified are different things"** and **"The
  repository is the source of truth; the website is a view of it"** each kept
  **once**, prominently (§3 and §6 respectively) instead of being repeated.
- **Final CTA** restates the product: *Keep the understanding with the code*,
  closing on *AI helped you build it. VibeKB helps you understand it.*
- **Footer** tagline updated to match the new hero.

## How the PHP confusion was resolved

- The self-demo framing *"…is a real application; VibeKB is the product
  explaining it"* was removed from the hero card note and the carousel intro.
- The hero example card is relabelled **"VibeKB analyzing its own repository,"**
  and its note says the metrics come from VibeKB's own repository-owned model.
- The carousel intro now says the records come from this repository's
  `.vibekb/` model and is *"not a hand-written product tour."* **No PHP is
  mentioned in that section.**
- All PHP/runtime details (PHP 8.2, shared hosting, no DB, no build step, no
  rewrite rules) were moved out of the body flow into a single collapsed note in
  §6, **"How this reference implementation runs,"** explicitly framed as a
  property of *this* implementation for contributors — not the product category
  and not a constraint on the repositories VibeKB can describe.

## How the AI-assisted Git workflow is now explained

- §2 names the tools (Claude Code, Cursor, Codex, Copilot, Gemini CLI) and the
  multi-session problem directly.
- §5 states VibeKB is used *with* coding agents and walks Understand → Record →
  Implement → Verify → Update & hand off.
- §6 explains the model is committed with the code, reviewed in Git, and read/
  updated by humans and agents, with the guide as a presentation layer.

## Interactions retained / removed

- **Retained:** the four-part model tabs (`data-tabs="outcomes"`), the live
  functionality carousel (`data-guide-preview`), the 5-stage workflow
  (`data-pipeline`/`data-timeline`), and the `.vibekb/` directory map
  (`data-repo-map`). Each earns its place by helping comprehension.
- **Removed from the page:** the problem stepper, the depth selector, the
  six-question relevance filter, the audience compare grid, and the principle
  manifesto — all redundant with the consolidated copy.
- **`assets/js/homepage.js` and `assets/css/homepage.css` were left unchanged.**
  The removed widgets' bind functions early-return when their root elements are
  absent (guarded by `$root.length`), so no dead code runs and no styling
  breaks. The rewrite reused existing classes only — no new CSS was required.

## Dynamic data confirmation

- Hero metrics (functions modelled, capability groups, % verified from source,
  active warnings) are still computed from the loaded `.vibekb/` content — a
  render check reports **17** functions modelled, matching the 17 records.
- The "AI now" chip still reflects the real current-work record.
- The carousel is still built from actual functionality records in index order,
  with real status/verification badges and real step-by-step flows. **No dynamic
  metric was replaced with invented text.**

## No unsupported claims added

- No automatic/autonomous extraction is claimed. The "V1, honestly" note states
  agents maintain the model, VibeKB detects change but does not interpret it, and
  `updates_automatically` stays `false`.
- No PHP-only framing, no fake testimonials/stats/companies, no pricing, no
  account or repository-import features.

## Validation performed

- `php -l index.php` — no syntax errors.
- Rendered `index.php` against loaded `.vibekb/` (73 KB, hero + carousel + all
  seven sections present; no PHP-as-category language in the body).
- Degraded paths: missing `manifest.json` renders normally; a missing `.vibekb/`
  directory renders without a fatal error (carousel omitted, hero card degrades
  to zeroed metrics).
- All guide links are relative (`guide/?view=…`) — subfolder-safe; no absolute
  `/…` hrefs.
- Removed widgets confirmed absent from output; retained widgets confirmed
  present with their no-JS fallbacks intact.
