# VibeKB Token Economics

An evidence-based evaluation of whether the additional token cost of maintaining
and using VibeKB pays for itself in savings, accuracy, continuity, and reduced
rework — measured against this repository, not assumed.

- **Method:** token counts are approximated as **characters ÷ 4** and are
  explicitly labelled *approximate*. They are directionally reliable for
  comparison, not exact billing. Measured facts (file sizes, command output
  sizes, deterministic-vs-LLM boundaries) are separated from **modelled
  estimates** (exploration costs, per-change costs) throughout.
- **Source commit analysed:** the working tree at the time of writing
  (2026-07-22). Regenerate the numbers with the appendix script.

---

## 1. Executive conclusion

**Is the current version token-positive, -neutral, or -negative?**
It depends almost entirely on **one variable: how often work crosses a session /
context boundary** (a new agent, a `/clear`, a handoff, a returning human).

- **Token-positive** for any repository worked across **more than ~6–15 fresh
  sessions or handoffs** (see the break-even model in §6). VibeKB's recurring
  maintenance overhead is small and bounded (~2–7K tokens per change), while the
  re-exploration it avoids is large and recurring (~15–40K+ per fresh session on
  this repo).
- **Token-neutral to slightly negative** *within a single long-lived session*
  where the same agent already holds the codebase in conversation context. There,
  VibeKB's read-benefit is near zero (the agent already knows) but its write-cost
  persists. **VibeKB's value is realised at session boundaries, not inside one.**
- **Token-negative** for a genuine **one-off** (1–3 sessions, no handoffs): the
  one-time bootstrap cost never amortises.

**Is it still worthwhile when quality and risk are included?** Yes, in a wider
band than tokens alone suggest. The measured **~20× compression** of targeted
understanding (§3) also prevents the expensive failure modes — missed
dependencies, hallucinated architecture, regressions, repeated/abandoned work —
whose cost is not counted in the token ledger but is very real. For repositories
with **frequent agent handoffs**, VibeKB is strongly worthwhile on both axes.

**One-line verdict:** *VibeKB is a bet that a project will be understood more than
once. When that is true it is token- and quality-positive; when it is not, it is
overhead.*

---

## 2. Current implementation analysis — how VibeKB spends tokens

The decisive architectural fact, confirmed by reading `tools/vibekb.php`:

> **The maintenance CLI is deterministic PHP.** `status`, `check`, `affected`,
> `validate`, and `generate` do their work with `git diff`, path-existence
> checks, a render-and-diff, and schema validation. **They consume zero LLM
> tokens.** The agent pays only to (a) *read* their compact text output and
> (b) *interpret and write* model updates. The tool's own honesty boundary says
> it "detects" mechanically and never "interprets."

This pushes the expensive-to-automate-but-cheap-as-a-script work (drift
detection, broken-reference checks, affected-file mapping, snapshot sync,
validation) entirely off the token budget.

> **Layout note (post repository-safety redesign).** Measurements below are of
> **this self-hosted VibeKB repository**, where the runtime still lives at the
> root (`guide/`, `tools/`) and agents orient via root `CLAUDE.md`. A **target
> repository** after `vibekb install` keeps that same runtime under
> `.vibekb/runtime/`, reference docs under `.vibekb/reference/`, and the
> integration prompt at `.vibekb/prompts/INTEGRATE_VIBEKB.md`. Session-start
> orientation there is `php .vibekb/runtime/tools/vibekb.php status` plus any
> managed block already present in the project's own `AGENTS.md`/`CLAUDE.md` —
> VibeKB does not own those files. See `docs/REPOSITORY_SAFETY.md`.

**What is efficient (measured):**

- **Compact command surface.** `status` output ≈ **428 tok**, `check` ≈ **237
  tok**, `affected` ≈ **220 tok**. These summaries are what the agent reads
  *instead of* scanning the repo.
- **Provenance stored once** in `manifest.json`, not repeated per record. Records
  that mention provenance are *about* provenance, not carrying a copy of it.
- **Structured front matter** (relationships as arrays: `files`, `reads`,
  `writes`, `depends_on`, `related_memory`) instead of prose — this is what makes
  deterministic `affected` mapping possible.
- **State separated from history.** `work/current.md` (170 tok) and
  `work/handoff.md` (909 tok) are small; session history lives in
  `work/sessions/` and is on-demand, not session-start.
- **Instructions explicitly discourage exhaustive reading** — CLAUDE.md:
  *"That is your orientation — you do not need to read every file first."*

**What is wasteful / risky (measured):**

- **Generated `docs/` is 5.8× the source it mirrors** (~206K tok vs ~35.5K tok
  of `.vibekb/`). It must be committed (it is the GitHub Pages snapshot), but if
  an agent ever greps or reads `docs/*.html` into context it pays up to ~206K
  tokens for knowledge it can get from ~1–4K tokens of source records. **There is
  no `.gitignore` and no explicit rule telling agents not to load `docs/` or
  `examples/`** (~56.6K tok of fixtures). This is the single largest *latent*
  waste vector. (Addressed by the §8 guardrail.)
- **Diagrams are stored as SVG** (~4.3K tok across three assets) plus JSON
  topology (~5.2K tok). Reasonable, but SVGs should stay on-demand, never
  session-start.
- **The bootstrap is genuinely expensive** (§4) — a real cost that must be
  amortised, not waved away.

**What is unknown / estimated:**

- Real tokenizer counts (we use chars/4).
- Actual "without VibeKB" exploration cost — modelled from file sizes and one
  measured subsystem comparison, not from A/B telemetry.
- How disciplined a given agent is about selective retrieval (the guardrail
  reduces the variance but cannot eliminate it).

---

## 3. Measured repository context

Approximate tokens (chars ÷ 4), from the appendix script over `git ls-files`.

### 3.1 Whole repository by kind

| Kind | ~tokens | Share | Notes |
|---|---:|---:|---|
| **generated** (`docs/`) | 206,085 | 51% | render of `.vibekb/`; **never** load into context |
| **code** (`guide/`, `tools/`, `assets/`, `index.php`) | 85,452 | 21% | on-demand |
| **example** (`examples/`) | 56,595 | 14% | fixtures; **never** load |
| **source** (active `.vibekb/`) | 35,508 | 9% | the model itself |
| **instruction** (CLAUDE/AGENTS/root docs/.cursor) | 18,760 | 5% | mostly on-demand |
| **Total tracked** | **~402,365** | | |

### 3.2 By session-loading behaviour

| When loaded | ~tokens | What |
|---|---:|---|
| **always** (floor) | 5,732 | CLAUDE.md + manifest + `project/` + `work/` state |
| **affected** (selective) | 27,118 | functionality/system/memory records, pulled by relevance |
| **ondemand** | 105,719 | code, guide, root docs, session history, SVGs |
| **never** (should not enter context) | 263,831 | `docs/` + `examples/` |

> **65% of the repository (the ~264K "never" tokens) should never touch an agent
> context window.** The economics are healthy *if and only if* that boundary
> holds.

### 3.3 The active model (`.vibekb/`) — 56 files, ~35,508 tok

Largest single records are `important-files.json` (3,030), the two diagram
topologies (1,904 + 1,661), and the SVG assets (~1,650 each). Functionality
records cluster at **~500–1,050 tok each**; memory records at **~360–480 tok**.
No single record dominates; retrieval is naturally granular.

### 3.4 The honest session-start floor

The "always" set is ~5.7K tok, but a well-behaved agent that trusts `status`
does **not** need to read every `project/` file — the 428-tok `status` output
already surfaces provenance, current work, handoff next-action, and drift. The
realistic floor is:

> **CLAUDE.md (~1,545) + `status` output (~428) ≈ ~2,000 tok** to orient,
> expanding to ~5.7K only if the agent reads the full `project/`+`work/` set.

---

## 4. Baseline comparison — with vs without VibeKB

### 4.1 Measured anchor (this repository)

A fresh agent asked *"how does drift detection work here?"* without a model must
read the implementing source:

| Read from source | ~tokens |
|---|---:|
| `tools/vibekb.php` | 6,336 |
| `guide/lib/Content.php` | 12,497 |
| `tools/generate-static.php` | 1,966 |
| `tools/validate.php` | 1,280 |
| **Total to understand one subsystem** | **~22,080** |

The equivalent VibeKB record, `functionality/records/detect-drift.md`, delivers
that understanding in **~1,044 tok** — a **~20× compression** for a targeted
question, *measured*, not assumed. Even allowing that the agent might not read
every file fully, the ratio stays in the ~10–20× range.

### 4.2 Recurring costs on each side

**Without VibeKB**, every fresh session / handoff re-pays some of:
searching, rediscovering architecture, tracing dependencies, re-reading the same
files, reconstructing prior decisions, understanding unfinished work, recovering
from context loss, and fixing regressions from incomplete understanding. On this
repo, building a working mental model from scratch is modelled at **~15–40K
tokens** for a fresh agent (small subsystem focus at the low end; broad
orientation at the high end), and **it recurs** because nothing is persisted.

**With VibeKB**, the recurring costs are: reading the compact CLI output
(~200–430 tok each), selectively reading relevant records (~2–4K), interpreting
the change, and writing updates + handoff (output tokens, §5). These are
**bounded and mostly proportional to the change**, not to the repository size.

---

## 5. Scenario analysis

Estimates in agent tokens. **Reading = input; writing = output.** Output tokens
are typically ~4–5× the price of input, so "write" costs are weighted as the
expensive side even where the raw token count looks small. Confidence is stated
per scenario. All numbers are modelled except where tied to §3–§4 measurements.

### Scenario 1 — Fresh agent, unfamiliar repository
| | Without VibeKB | With VibeKB |
|---|---|---|
| Orientation | ~15–40K (explore) | ~2K (`status` + CLAUDE.md), +2–4K selective records |
| **Net** | — | **Saves ~10–35K** |

Quality: VibeKB agent starts from current, verified reality incl. warnings and
constraints; the explorer may form wrong assumptions. **Confidence: high** (anchored by §4.1). Changes if the repo is tiny (exploration is already cheap).

### Scenario 2 — Returning agent, one small bug fix
- VibeKB overhead: `status`(428) + `affected`(220) + read 1 record(~700) + update 1 record(~600 out) ≈ **~2K**.
- Without: re-locate the code, re-trace the one flow ≈ **~5–15K**.
- **Net: saves ~3–13K.** Quality: `affected` surfaces the record's `depends_on`
  and warnings, reducing regression risk. **Confidence: medium-high.** Changes if
  the same agent still has the file in context (then WITH-overhead is ~pure cost).

### Scenario 3 — New feature spanning several components
- VibeKB overhead: read system/ + 3–5 records (~4–6K) + write/extend 2–4 records + handoff (~2–3K out) ≈ **~7–12K**.
- Value: the `files[]`/`depends_on` graph makes **missed dependencies** visible
  before coding. A single prevented missed-dependency regression (re-diagnose +
  re-fix + re-review) commonly costs **10–30K+** on its own.
- **Net tokens: roughly neutral to modestly negative in isolation; strongly
  positive once one regression is avoided.** **Confidence: medium** (depends on
  model quality). Changes if the model is stale (§5, Scenario 5).

### Scenario 4 — Handoff after incomplete work
- Continue from raw repo: reconstruct *what was in progress and why* from diffs
  and code ≈ **~15–40K**, with real risk of redoing or contradicting the prior
  agent.
- Continue from VibeKB: read `current.md`(170) + `handoff.md`(909) + affected
  records ≈ **~2–3K**, with the exact next action stated.
- **Net: saves ~12–37K and removes duplicate/abandoned-work risk.** **Confidence:
  high.** This is VibeKB's strongest case.

### Scenario 5 — VibeKB has drifted out of date (worst case)
- `check` **deterministically** flags drift (changed files since the recorded
  commit, broken references, stale `/docs`) for ~237 tok — so drift is *detected*
  cheaply and is hard to miss.
- Danger is **silent** staleness an agent trusts: a wrong record can send it in
  the wrong direction — *actively* worse than no model, not merely unhelpful.
- Recovery cost: re-verify the affected slice (read source for the changed
  records + rewrite them) ≈ **~5–20K** depending on drift breadth.
- **Net: negative during drift; the mitigation is that detection is free and
  loud, and verification states (`not-verified`, `needs-verification`) cap
  misplaced trust.** **Confidence: medium.** This is the primary risk to manage.

---

## 6. Break-even model

Let (all in approximate tokens):

- `B` = one-time **bootstrap** cost (analyse repo + author initial model).
- `S` = **saving per fresh session / handoff** = (without-VibeKB orientation) −
  (VibeKB orientation).
- `M` = **maintenance overhead per change** (read + write model updates).
- `N` = number of fresh sessions / handoffs over the project's life.
- `C` = number of changes over the project's life.

**VibeKB is token-positive when:**

```
   N · S   >   B  +  C · M
   ────────────────────────
   savings  >  bootstrap + upkeep
```

Rearranged for the break-even session count:

```
   N*  =  ( B + C · M )  /  S
```

**Plugging in evidence-based ranges for this repo** (medium-small, ~40 code
files):

| Term | Estimate | Basis |
|---|---|---|
| `B` bootstrap | ~150–250K | authoring ~35.5K tok of model ≈ 4–7× in read+write effort |
| `S` per-session saving | ~12–35K | §4.1 anchor + §5 scenarios 1 & 4 |
| `M` per-change upkeep | ~2–7K | §5 scenarios 2 & 3 |

Assuming changes ≈ sessions (`C ≈ N`) and taking mid-points
(`B≈200K, S≈20K, M≈4K`):

```
   N* = 200K / (20K − 4K) ≈ 12.5 sessions/handoffs
```

- **Break-even ≈ 6–15 fresh sessions/handoffs** across the plausible range.
- Below that (a one-off, `N ≈ 1–3`): **token-negative** — the bootstrap dominates.
- Well above it (an actively developed, frequently-handed-off repo): **clearly
  positive**, and the margin widens with every handoff because `S` recurs while
  `B` is paid once.

**How the variables move break-even (evidence-tied):**

| Variable | Direction | Effect on `N*` |
|---|---|---|
| Frequent handoffs / `/clear`s | ↑ `S` | **lower** (better) — VibeKB's sweet spot |
| Same agent keeps context all project | ↓ `S`→~0 | **∞** — VibeKB can't pay off in-session |
| Larger repo | ↑ `S`, ↑ `B` | net **lower** `N*` — bigger repos amortise faster |
| Loading `docs/`/`examples/` into context | ↑ effective `M` | **higher** — the §8 guardrail protects this |
| More deterministic automation | ↓ `M` | **lower** |
| Model drift | ↑ `M` (recovery) | **higher** — keep it fresh |
| Prompt caching the ~2–5.7K floor | ↓ effective read cost | **lower** |

> **There is no universal number.** For this repository under stated assumptions,
> **~a dozen sessions/handoffs** is the honest break-even. The framework, not the
> number, is the deliverable.

---

## 7. Risk-adjusted value

- **Token ROI:** positive beyond ~6–15 sessions/handoffs (§6). Negative for
  one-offs and for single-session same-context work.
- **Quality ROI (not in the token ledger):** fewer incorrect changes,
  hallucinated architecture, and missed dependencies; the `depends_on`/`files[]`
  graph and warnings act before code is written.
- **Continuity ROI:** the strongest, most defensible axis (§5, Scenario 4).
  `current.md` + `handoff.md` + verification states convert an expensive,
  error-prone handoff into a ~2–3K-token continuation with a stated next action.
- **Error-prevention value:** a single avoided regression (re-diagnose + re-fix +
  re-review, ~10–30K+) can outweigh an entire project's maintenance overhead. This
  is where VibeKB "costs more tokens but is still worth it" — most clearly in
  Scenario 3.

---

## 8. Optimization recommendations (ranked)

Impact × difficulty. "Now" = implemented in this change; "Next"/"Later" = planned.

| # | Recommendation | Impact | Difficulty | When |
|---|---|---|---|---|
| 1 | **Context-economy rule**: never load `docs/` or `examples/` into working context; trust `status`/`affected` for selective retrieval; keep session-start to the compact `status` output + handoff. | **High** (protects the 264K "never" boundary) | Low | **Now** |
| 2 | Keep the honest session-start floor documented (~2K, not the full 5.7K set). | Medium | Low | **Now** (this report + rule) |
| 3 | Add `php tools/vibekb.php context` — print the measured budget: session-start floor, current affected-scope size, and the "never" set, so the boundary is visible and enforceable. Promote the appendix script to a small deterministic tool. | Medium-High (exposes economics as a feature) | Medium | **Next** |
| 4 | Prompt-cache / reuse the always-loaded floor across turns where the harness allows. | Medium | Low-Medium | **Next** |
| 5 | Operating modes by repo size (lean vs full retrieval scope). | Medium | Medium | **Later** |
| 6 | Optional compact machine-readable record projection for large-repo affected-scope reads. | Low-Medium | Medium-High | **Later (experiment)** |

Explicitly **not** recommended: collapsing prose records into pure data (the
prose is the product — human-readable understanding), or dropping `/docs` (it is
the static deliverable). These are *useful* redundancy, not waste.

---

## 9. Recommended operating model

- **Read at session start (target ~2K tok):** CLAUDE.md + `php tools/vibekb.php
  status`. Expand to `project/` + `handoff.md` only when the task needs it.
- **Retrieve only when relevant:** functionality/system/memory records for the
  **affected** scope, found via `php tools/vibekb.php affected <files>` — not the
  whole model.
- **Update after a change:** the affected functionality records, their statuses
  and verification states, related memory, and the handoff. Proportional to the
  change, never a full rewrite.
- **Run deterministically (0 LLM tokens):** drift detection, broken-reference
  checks, affected-mapping, validation, `/docs` regeneration, snapshot sync,
  topology test — i.e. `status` / `check` / `affected` / `validate` / `generate`.
- **Never place into routine context:** `docs/` (generated), `examples/`
  (fixtures), diagram SVGs, and session history — pull the last two only on
  explicit demand.

---

## 10. Go / no-go

**Go — with a stated condition.** The current architecture is economically
viable *because the expensive mechanical work is already deterministic and the
per-change overhead is small and proportional.* It becomes clearly token-positive
for any repository worked across more than roughly a dozen sessions/handoffs, and
is quality/continuity-positive well before that for handoff-heavy work.

**What must hold for the economics to stay favourable:**

1. The **"never" boundary** (264K tokens of `docs/` + `examples/`) must not leak
   into agent context — enforced by the §8 rule added in this change.
2. **Selective retrieval** must be the default — session-start ~2K, affected-scope
   reads, not whole-model loads.
3. The model must be **kept fresh**; drift is cheap to detect (`check`) but
   dangerous if silently trusted.

**Not-go cases:** genuine one-offs (1–3 sessions, no handoff) and single-session
same-context work — there VibeKB is overhead; skip it or defer bootstrap.

**Evidence that would prove the economics (future work):** real-tokenizer counts
replacing chars/4; A/B telemetry of task token totals with vs without the model
on the same changes; and measured regression-prevention rates. Until then, the
strongest hard evidence in hand is the **measured ~20× compression** of §4.1 and
the **deterministic (0-token) CLI** of §2.

---

## Appendix — measurement methodology

Numbers come from a deterministic pass over `git ls-files`, classifying each
tracked file by area, kind (source / generated / instruction / code / example),
and session-loading behaviour (always / affected / ondemand / never), with
`~tokens = round(chars / 4)`. Command-output sizes (`status`, `check`,
`affected`) were measured by piping each command's stdout to a byte count. The
subsystem-compression anchor (§4.1) compares the byte size of the implementing
source files against the corresponding functionality record.

`chars ÷ 4` is a documented approximation used where a real tokenizer is
unavailable; treat all token figures as approximate and directional. Re-running
the classification after content changes will refresh every table here.
