# Homepage layered redesign — plan

## Current homepage content inventory

| Section | Content | Disposition |
|---------|---------|-------------|
| Hero | Brand, “Your agent built it…”, CTAs | Rework to Layer 1 promise + pipeline visual |
| Problem | Late-night feature / lost chat / responsibility | Keep as Layer 1 narrative; add interactive steps + README expandable |
| Trick | Agent updates explanation with code | Move into How it works / agents (Layer 2–3); do not claim unsupported automation |
| How it works | 3 steps (agent, website, next prompt) | Expand into selectable pipeline with V1 vs architecture-direction labels |
| Demo | Screenshot + CTA | Replace with interactive Project Guide preview (excerpts from real guide) |
| Not docs | Docs vs explanation | Fold into product philosophy / principles |
| Explains | Bullet list of lost project parts | Map into four product outcomes (tabs/cards) |
| Audience | Solo AI builders | Fold into Developers and AI agents comparison |
| Version 1 | One demo, no accounts/cloud APIs | Keep as honest scope callout in How it works / final CTA |
| Final CTA | Open guide | Keep, strengthen |

**Repeated:** Multiple “open the guide” CTAs (keep strategically). Problem/agent themes repeat (consolidate into problem + agents sections).

**No JS today:** Landing is static PHP + CSS only (`landing.css`). No `assets/js/`.

## Information hierarchy

### Layer 1 — Immediate (default visible)

- What VibeKB is / promise
- Problem in short form
- Project Guide visual preview entry
- Primary CTA into sample

### Layer 2 — Product understanding (scroll + light interaction)

- Four outcomes (Understand / Follow / Change / Debug)
- Three depths demonstration
- How VibeKB works pipeline
- Developers vs AI agents (summary)
- Relevance inclusion tests (short)

### Layer 3 — Technical / strategic (expandable)

- Why READMEs are not enough
- Token-efficiency explanation
- What VibeKB leaves out
- Repository file map details
- Scene / knowledge-model notes
- Principles manifesto (one at a time when enhanced)
- Architecture direction vs Version 1 capabilities

## Immediately visible

Hero, short problem default, product outcome titles, sample guide intro + first preview chapter, depth labels, pipeline stage titles, inclusion test titles, audience summary, principles titles (or first principle), final CTA.

## Interactive

Problem step sequence; product outcome tabs; guide chapter preview; depth selector; pipeline stages; inclusion tests; repo directory selectors; principles stepper; developer/agent toggle.

## Expandable

Why READMEs are not enough; What VibeKB leaves out; Token efficiency; Architecture direction notes; Version 1 boundaries.

## Links into Project Guide

- Primary CTAs → `guide/`
- Preview “Open complete guide”
- Outcome cards → relevant chapter hashes (`#what-is-this`, `#save-flow`, `#problems`, `#change-safely`, etc.)

## Mobile behavior

Single column; stacked diagrams; full-width controls; accordion over side-by-side; no horizontal overflow; compact header with wordmark + Project Guide CTA; preview chapters as stacked controls + panel.

## JavaScript fallback

PHP renders full section content. Without JS, use `<details>`, lists, and natural scroll. Enhanced mode (`html.js`) collapses inactive panels into shared display areas. Hidden states applied only after `VibeKBHomepage.init()` succeeds.
