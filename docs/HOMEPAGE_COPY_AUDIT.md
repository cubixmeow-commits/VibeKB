# Homepage copy audit (pre-rewrite)

A working audit of `index.php` before the positioning / information-architecture /
copy pass. This file is a process artifact; the static generator deliberately
leaves `docs/*.md` untouched, so it is safe here.

## 1. The current homepage's main message

The page opens strongly — "Understand what your software is doing," positioned as
"a living explanation for AI-assisted software" that lives in the repository and
is organized around functionality. That opening is accurate and worth keeping.

But across eleven sections the message drifts. The page ends up trying to explain
three different things at once:

1. What VibeKB is (the product).
2. What the self-demo application is (the example being explained).
3. How VibeKB itself is technically implemented (PHP 8.2, shared hosting, no DB,
   directory tree, runtime constraints).

By the lower half of the page the technical/implementation story competes with
the product story, and the central proposition is diluted rather than reinforced.

## 2. The strongest existing copy worth preserving

- **"AI can change six files faster than you can rebuild your mental model."**
  The single best line on the page. It should become the spine of the homepage.
- **"Intended, implemented, and verified are different things."** Strong, true,
  and product-defining — but keep it **once**, prominently, not repeated.
- **"The repository is the source of truth; the website is a view of it."**
  Sharp articulation of the whole architecture. Keep.
- The **hero example card** with live metrics (functions modelled, capability
  groups, % verified from source, active warnings) — real, computed from loaded
  content. Keep; only relabel it.
- The **real functionality carousel** — genuine proof, driven by actual records.
  Keep the mechanism; rewrite the intro.
- The **five-stage workflow** (Understand → Record → Implement → Verify → Update &
  hand off) — accurate to the maintenance lifecycle. Keep, adapt.
- The **"Version 1, honestly"** disclosure — keeps the page honest about
  agent-maintained extraction. Keep.

## 3. Every major source of confusion or repetition

**Confusion**

- The self-demo framing: *"[Project] is a real application; VibeKB is the product
  explaining it."* Combined with the visible PHP/runtime emphasis, this invites
  the reading that VibeKB is *for PHP*, or that the PHP app *is* the product.
- Runtime details (PHP 8.2, shared hosting, no Node, no database, no build step)
  are presented as **product benefits** in the architecture section, before the
  visitor has understood how VibeKB fits their own AI coding workflow.
- The page occasionally reads like the guide itself rather than a product landing
  page for it.

**Repetition** — the same handful of ideas are re-explained in separate sections:

- Section 2 "questions VibeKB answers" (what does it do / how / what is AI
  changing / is it done).
- Section 3 "four things you can see" (what it does / how it works / what AI is
  changing / why).
- Section 7 "every functionality page answers" (six near-identical questions).
- Section 5 depths, Section 8 audience, Section 10 seven principles — all restate
  the same value in new words.

Eleven sections make the proposition feel *less* clear, not more complete.

## 4. Claims that could overstate V1

- Anything implying **automatic** extraction/scanning. V1 is **agent-maintained**:
  it detects that code changed, it does not interpret the change. The rewrite must
  preserve this (the "V1, honestly" note and the workflow's Verify step already
  do; keep them and avoid "automatic understanding" language elsewhere).
- Overuse of "living" without saying *what* stays current and *how*. Tie it to the
  agent workflow instead of asserting autonomy.

## 5. Proposed new section order (eleven → seven)

1. **Hero** — define the category: repository understanding for AI-assisted
   development. Headline: *Understand the software AI helped you build.* Live
   self-repo example card (relabelled).
2. **The problem** — *AI can change six files faster than you can rebuild your
   mental model.* The real multi-session workflow problem, and who it's for.
3. **What VibeKB adds to the repository** — four coherent parts of one model:
   Current functionality · How it works · Active AI work · Repository memory.
   (Consolidates old §2, §3, §7.)
4. **Live proof: VibeKB explains itself** — the real functionality carousel, new
   intro, no PHP mention.
5. **How it fits into AI-assisted development** — Understand → Record → Implement
   & verify → Update & hand off, with the honest V1 disclosure. (Old §6.)
6. **Why the repository-owned model matters** — *Chat context expires. Repository
   context remains.* Merges the strongest of depth, architecture, principles
   (reduced to three trust principles); directory tree **after** the "why";
   PHP/runtime moved into a small labelled implementation note. (Old §5, §9, §10.)
7. **Final CTA** — *Keep the understanding with the code.* Explore the guide /
   view on GitHub.

Removed as standalone sections: depths (§5), the six-question record breakdown
(§7), the audience/compare grid (§8), the seven-principle manifesto (§10) — their
substance is folded into §2, §3, and §6.

## 6. Product category, in one statement

**VibeKB is a repository-native understanding layer for AI-assisted software
development** — added to a Git repository and maintained alongside the code,
giving humans and coding agents a structured, evidence-aware explanation of what
the software does, how it works, what AI is changing, and what has been verified.

## 7. Four things the page keeps conflating (kept distinct in the rewrite)

- **VibeKB — the repository-native model.** The product: the `.vibekb/` living
  software model plus the workflow that keeps it true. Repository-owned,
  human-readable, AI-editable, versioned with the code.
- **The generated guide.** A *view* of that model (dynamic Mode A / static
  Mode B). Not the product; a rendering of it.
- **The current self-demo.** VibeKB is self-hosted: the active model describes
  VibeKB itself, so the live example on the homepage is *VibeKB analyzing its own
  repository* — proof it works on a real repo, not a claim about PHP.
- **The PHP reference implementation.** How *this particular* guide happens to
  run (PHP 8.2, shared hosting, no build step). An implementation detail for
  contributors — not what VibeKB is for, and never the product category.
