# Project Guide engine

How the reusable VibeKB Project Guide presentation system works.

## Purpose

The Project Guide explains a software project in a guided sequence of chapters.
Content lives in repository-owned structured files. PHP renders a complete readable page. JavaScript and jQuery enhance navigation and interaction.

This is not a generic page builder, documentation portal, or slide deck framework.

## Data layout

```
.vibekb/
  project.json                 # Project identity (shared)
  guide/
    guide.json                 # Guide metadata + chapter file list
    chapters/
      01-what-is-this.json
      …
  overview/ decisions/ …       # Reference knowledge (Markdown)
```

### `guide.json`

| Field | Purpose |
|-------|---------|
| `title` | Full guide title |
| `short_title` | Compact label (e.g. Project Guide) |
| `subtitle` | One-line promise |
| `intro` | Optional supporting paragraph |
| `version` | Guide schema/content version |
| `storage_key` | localStorage key for last chapter |
| `chapters` | Ordered list of chapter file stems under `chapters/` |

### Chapter files

Each chapter JSON includes:

| Field | Purpose |
|-------|---------|
| `id` | Stable id (used for URL hash) |
| `number` | Display number |
| `question` / `title` | Human question answered by the chapter |
| `summary` | Short chapter summary |
| `scenes` | Ordered list of scene objects |

## Supported scene types

| Type | Role |
|------|------|
| `statement` | Large headline, body, optional facts |
| `progression` | Ordered visual stages |
| `flow` | Step sequence with optional lightweight demo |
| `interactive-cards` | Expandable explanation cards |
| `concept-map` | Layered system picture with focus states |
| `problem-path` | Troubleshooting walkthroughs / alignment |
| `checklist` | Change-safety checklists or “affects” lists |
| `developer-detail` | Work-on-it disclosure or question entry points |
| `reference-links` | Links into the technical reference |

Each scene type has:

1. A PHP template under `guide/templates/scenes/`
2. Optional jQuery behavior in `guide/assets/js/project-guide.js`
3. A readable non-JavaScript fallback (visible content or `<details>`)

## Depth model

Reuse one knowledge model at three depths:

| Depth | Where | Contains |
|-------|-------|----------|
| Understand | Visible by default | Purpose, UX, mental model, relationships |
| Work on it | Developer-detail controls | Responsibilities, invariants, risks, change impact, debugging starts |
| Reference | End links / `edition/` | Full articles, glossary, history |

`source` refs (`collection` + `slug`) resolve through `ContentRepository` to existing Markdown knowledge files. Prefer curated bullets in chapter JSON plus links—do not paste entire articles into every scene.

## Progressive enhancement

**Without JavaScript**

- Every chapter is in the HTML and can be scrolled
- Developer details remain reachable via `<details>` or visible lists
- Links to the technical reference work
- No essential content is JS-only

**With JavaScript / jQuery**

- One chapter is emphasized at a time
- Previous / Continue controls and progress rail
- URL hash routing + history back/forward
- Keyboard: ← → chapters, Escape closes panels (ignored while typing)
- Flow / concept-map / troubleshooting progression
- Last chapter remembered in `localStorage`
- `prefers-reduced-motion` skips decorative transitions
- Focus moves to the active chapter heading

## Creating a Project Guide for another repository

1. Keep or create `.vibekb/project.json`.
2. Add `.vibekb/guide/guide.json` and chapter JSON files.
3. Point content at real decisions, risks, and debugging notes for that project.
4. Reuse the `guide/` PHP engine (or copy it into the new repo).
5. Link the landing page to `/guide/`.
6. Keep a technical reference path for full articles if needed.

Do not invent features the codebase does not support.

## Accessibility requirements

- Semantic HTML and logical heading order
- Skip link
- Real buttons for actions; real links for navigation targets
- Visible `:focus-visible`
- `aria-expanded` / `aria-controls` on disclosures
- Live region for chapter changes
- No hover-only interactions
- Text alternatives for diagrams
- Sufficient contrast; responsive type
- Reduced-motion support
- Do not communicate essentials by color alone

## Adding a new scene type

1. Document the JSON shape in this file.
2. Add `guide/templates/scenes/<type>.php` with semantic HTML and a no-JS fallback.
3. Register the type in `GuideRenderer::$allowed` list.
4. Add enhancement in `project-guide.js` only if interaction is needed.
5. Style in `project-guide.css` without relying on motion for meaning.

## What should not be included

- Exhaustive file or function inventories
- Generic framework tutorials
- Invented product features
- Permanent GitHub-style category sidebars as the primary UX
- Knowledge that exists only inside JavaScript
- Newspaper / IDE / docs-portal chrome as the main metaphor

See also [PROJECT_GUIDE_CONTENT_RULES.md](./PROJECT_GUIDE_CONTENT_RULES.md).
