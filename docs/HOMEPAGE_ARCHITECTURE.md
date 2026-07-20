# Homepage architecture

## Purpose

The root homepage explains **VibeKB the product** and demonstrates the **Project Guide**.
It is not a second Project Guide and not a documentation portal.

**Primary audience:** people building with Cursor, Claude Code, Windsurf, Copilot, and similar tools.

**Strategic narrative:** The homepage educates visitors about a new reality first—code is generated faster than humans can absorb it—then introduces VibeKB as the way to close that gap. Layer 1 is the problem story; VibeKB appears after the shift is clear. The Project Guide is framed as preserved understanding (not documentation). Homepage sample display name may differ from the in-guide project name (e.g. “Weekend SaaS Demo” → SaaS Idea Manager).

## Information layers

| Layer | When shown | Job |
|-------|------------|-----|
| 1 Immediate | Default | Promise, problem, path into the sample |
| 2 Product | Scroll + light interaction | Outcomes, depths, pipeline, audience, relevance |
| 3 Strategic / technical | Expandables & steppers | README limits, token efficiency, leave-outs, repo map detail, principles, V1 boundaries |

Rule: **assign every new fact to a layer before adding it.**

Relevant information does not need to be removed—but it must not all compete for attention at once.

## Section map

1. Hero — the new bottleneck (generation vs understanding), before the product  
2. The shift — story of how the gap opens (stepper)  
3. Closing the gap — introduce VibeKB as preserved understanding  
4. Sample Project Guide preview — what closing the gap looks like  
5. Depths — human absorption speed across Understand / Work on it / Reference  
6. Workflow — day-one timeline so the gap never opens  
7. Relevance — what to preserve while building  
8. Audience — “never want to lose the next project”  
9. Repository architecture — where the mental model lives  
10. Principles — rules for keeping understanding ahead of generation  
11. Final CTA — thesis + living guide  

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
