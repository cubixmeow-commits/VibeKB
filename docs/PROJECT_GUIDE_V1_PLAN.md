# Project Guide V1 — Implementation Plan

## Current structure

```
/
  index.php                 # VibeKB marketing landing page
  assets/css/landing.css    # Landing styles
  edition/                  # Newspaper-style “Current Edition” publication engine
    bootstrap.php
    index.php / page.php
    lib/                    # ContentRepository, Markdown, FrontMatter, helpers
    templates/              # Collection pages + layout with sidebar
    assets/css/edition.css
  .vibekb/                  # Repository-owned knowledge
    project.json
    edition.json / homepage.json / collections.json
    overview/ decisions/ risks/ warnings/ assumptions/
    debugging/ modules/ mental-models/ glossary/ editorial/
  .cpanel.yml / DEPLOYMENT.md / AGENTS.md / README.md
```

The sample experience today is a publication-style site (`edition/`) with a GitHub-like collection sidebar (Modules, Decisions, Risks, etc.). Content is Markdown + YAML front matter under `.vibekb/`.

## Existing content sources (reuse, do not invent)

| Source | Use in Project Guide |
|--------|----------------------|
| `project.json` | Project name, tagline, stack, constraints |
| `overview/read-this-first.md`, `how-the-project-works.md`, `project-map.md` | Chapters 1, 3, 5 |
| `decisions/*` | Chapter 4 simplicity cards; change-safety |
| `assumptions/*` | Chapter 4 and developer details |
| `warnings/*`, `risks/*` | Chapters 4, 6, 7 |
| `debugging/*` | Chapter 6 troubleshooting sequences |
| `modules/*` | Chapter 8 curated entry points + reference |
| `mental-models/*`, `glossary/*`, `editorial/*` | Chapter 8 complete technical reference |
| `edition/` engine | Remains as secondary technical reference UI |

No application features beyond this knowledge will be invented (no fake auth, uploads, multi-user behavior, or schema details not already documented).

## Files that will be changed

- `index.php` — point demo CTAs to Project Guide; update demo wording
- `README.md` — document Project Guide as primary sample
- `DEPLOYMENT.md` — document `guide/` path and verification
- `.cpanel.yml` — review excludes; ensure `guide/` deploys and `.vibekb/` (including `guide/` content) remains included; note `docs/` stays excluded
- `edition/templates/layout.php` — retitle nav from “Current Edition” toward technical reference / Project Guide link (small wording only)

## Files that will be added

```
docs/PROJECT_GUIDE_V1_PLAN.md          # this file
docs/PROJECT_GUIDE_ENGINE.md
docs/PROJECT_GUIDE_CONTENT_RULES.md

.vibekb/guide/guide.json
.vibekb/guide/chapters/01–08 JSON chapter files

guide/
  index.php
  bootstrap.php
  includes/GuideLoader.php
  includes/GuideRenderer.php
  includes/helpers.php
  templates/shell.php
  templates/chapter.php
  templates/scenes/*.php   # nine scene types
  assets/css/project-guide.css
  assets/js/project-guide.js
```

Existing `.vibekb/` Markdown knowledge files are **preserved in place**. Chapter JSON curates and points at them; the `edition/` UI remains available as the complete technical reference.

## Content migration approach

1. Keep all existing Markdown knowledge files untouched as the Reference layer source of truth.
2. Author chapter JSON under `.vibekb/guide/chapters/` with Understand-level plain language drawn from those files.
3. Attach Work-on-it developer panels that either embed concise curated bullets or resolve `source` refs (`collection` + `slug`) via the shared loader.
4. Chapter 8 `reference-links` and `developer-detail` scenes deep-link into `edition/` collection pages for full articles.
5. Do not delete `edition.json` / `homepage.json`; “edition” remains a data concept for historical publication, not the primary UI label.

## Accessibility approach

- Semantic HTML: `main`, `section`, `h1`–`h3`, real `<button>` / `<a>`
- Skip link; logical heading order per chapter
- `aria-expanded` / `aria-controls` on disclosure and card controls
- Live region for chapter change announcements
- Visible `:focus-visible` styles; no hover-only interactions
- Text alternatives for diagrams (visually hidden or adjacent copy)
- `prefers-reduced-motion: reduce` disables transitions
- Focus moves to the active chapter heading when JS changes chapters

## JavaScript fallback approach

- PHP renders every chapter and every scene fully in the initial HTML
- Without JS: natural scroll through all chapters; `<details>` / visible panels for developer content; all links work
- With jQuery enhancement: presentation mode (one chapter emphasis), hash routing, keyboard nav, progress, interactive flow/step activation, localStorage last chapter
- Knowledge never lives only in JS
