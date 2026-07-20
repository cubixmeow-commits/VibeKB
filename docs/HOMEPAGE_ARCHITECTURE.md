# Homepage architecture

## Purpose

The root homepage explains **VibeKB the product** and demonstrates the **Project Guide**.
It is not a second Project Guide and not a documentation portal.

**Primary audience:** people building with Cursor, Claude Code, Windsurf, Copilot, and similar tools.

**Strategic position:** VibeKB is day-one infrastructure for AI-assisted development—a living Project Guide that grows with the repository—not a cleanup tool after understanding is already lost. The `#workflow` section is the central timeline (new project → initialize → build → guide grows → months later). Homepage sample display name may differ from the in-guide project name (e.g. “Weekend SaaS Demo” → SaaS Idea Manager).

## Information layers

| Layer | When shown | Job |
|-------|------------|-----|
| 1 Immediate | Default | Promise, problem, path into the sample |
| 2 Product | Scroll + light interaction | Outcomes, depths, pipeline, audience, relevance |
| 3 Strategic / technical | Expandables & steppers | README limits, token efficiency, leave-outs, repo map detail, principles, V1 boundaries |

Rule: **assign every new fact to a layer before adding it.**

Relevant information does not need to be removed—but it must not all compete for attention at once.

## Section map

1. Hero — promise + mini pipeline visual  
2. Problem — stepped narrative + README expandable  
3. Product outcomes — four tabs into guide chapters  
4. Sample Project Guide preview — shared chapter JSON excerpts  
5. Depths — Understand / Work on it / Reference on one feature  
6. How it works — pipeline with Version 1 vs architecture-direction badges  
7. Relevance filter — inclusion tests + leave-outs  
8. Developers & AI agents — comparison + token-efficiency expandable  
9. Repository architecture — `.vibekb` map  
10. Principles — one-at-a-time manifesto  
11. Final CTA — complete guide + repository  

## Interaction patterns

- Tabs / steppers for mutually exclusive explanations  
- Native `<details>` for deep panels  
- Guide preview prev/next + chapter buttons  
- Keyboard: arrow keys within tablists; Escape closes open details  
- Hash links for major sections  

Every interaction must improve comprehension—not decorate.

## JavaScript

- File: `assets/js/homepage.js`
- Namespace: `window.VibeKBHomepage`
- Requires jQuery (CDN)
- Adds `html.js` **only after** successful `init()`
- Enhanced UI hides fallback blocks via `html.js …` CSS
- Without JS, fallback lists/articles remain readable

## Progressive enhancement

| Without JS | With JS |
|------------|---------|
| Full scrollable content | Condensed shared panels |
| Details/accordions work | Tablists update in place |
| Guide preview shows all excerpt panels | One chapter at a time |
| All CTAs work | Same CTAs + keyboard tab behavior |

## Mobile

- One column under ~800px  
- Full-width primary actions  
- Stacked tab buttons  
- No hover-only content  
- No horizontal overflow (`overflow-wrap`, `min-width: 0`)  
- Compact header: wordmark + Project Guide CTA  

## Content boundaries

**Homepage may:** explain product, philosophy, architecture, and preview the sample.

**Homepage must not:** duplicate every sample chapter, invent unsupported automation, or replace the Project Guide.

**Project Guide:** explains the sample software project in depth.

## Preview data

`index.php` loads selected chapter JSON from `.vibekb/guide/chapters/` for the homepage preview.
Prefer shared structured content over disconnected marketing copy.

## Adding future material

1. Choose a layer (1–3).  
2. Prefer expanding an existing section over adding a new wall of text.  
3. Provide a no-JS fallback.  
4. Keep Version 1 claims honest (`Available in Version 1` vs `Architecture direction`).  
5. Update this document when section structure changes.
