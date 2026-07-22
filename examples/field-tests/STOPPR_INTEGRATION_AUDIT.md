# StopPR Integration Audit

A structured comparison of the canonical **VibeKB** product repository against
the **VibeKB-stoppr** field test, and a decision record for what to port back.

- **Canonical product:** `cubixmeow-commits/VibeKB` — defines the product, the
  `.vibekb/` schema, the maintenance workflow, initialization, agent rules, the
  SousMeow canonical example, the PHP guide renderer, and deployment behaviour.
- **Reference implementation:** `cubixmeow-commits/VibeKB-stoppr` — a real
  VibeKB integration into an existing Flutter/Firebase application (`Stoppr`),
  analysed read-only at commit `d5fc37c`.

> **Framing.** StopPR is **evidence of what worked**, not the new canonical data
> model. Its `.vibekb/` was itself modelled on VibeKB, and its static `/docs`
> was hand-rendered from VibeKB's own PHP templates. We extract the
> generalizable improvements, adapt them to VibeKB's design system, and reject
> anything StopPR-specific. **Audit first; copy nothing blindly.**

---

## 1. Side-by-side inventory

| Dimension | VibeKB (canonical, before) | VibeKB-stoppr (field test) |
|---|---|---|
| `.vibekb/` structure | project, functionality, system, files, memory, work | Same six top-level groups (structure preserved) |
| Manifest | `example_project` block; no generation/commit provenance | `project` block; no generation/commit provenance |
| Functionality records | 25 records (23 implemented, 2 partial) across 9 areas | 30 records across 10 areas |
| Record format | front matter A–Q body sections | Same shape, shorter bodies |
| Project records | identity, intent, current-state, constraints | Same |
| System records | mental-model, components, request-flow, data-flow, storage, deployment | Same |
| Memory records | decisions, constraints, assumptions, warnings, discoveries, changes | Same taxonomy |
| Work / handoff | current, handoff, sessions | Same; completion labelled "Current AI work: completed" |
| Important-file records | `files/important-files.json` | Same |
| Rendering | **Dynamic PHP guide** (`guide/`), query-string routed | **Static HTML** in `/docs` (hand-generated), no PHP |
| Page inventory | Overview, Functionality (+detail), Architecture, Data, Files, Current work, Changes, Decisions (+detail), Handoff, Reference | Same set **plus Diagrams and Search** |
| Navigation | Sidebar (Primary / Explore), skip link, mobile drawer | Same, plus a **Diagrams** primary item and a header search box |
| Responsive | Mature 1083-line `guide.css`, drawer nav | Reused VibeKB classes; shipped CSS only had ~79 lines of **diagram-only** rules (base tokens undefined) |
| Search | None | Client-side JSON index + `search.json`, but via **jQuery** |
| Styling / readability | Warm cream + teal design system, reading column | Same class names; relied on VibeKB tokens that were **not shipped** |
| Static assets | `guide/assets/{css,js}` served by PHP | `docs/assets/{css,js,data,diagrams}` |
| Deployment | cPanel `.cpanel.yml` + `.vibekb/.htaccess` guard | GitHub Pages, `Settings → Pages → /docs` |
| GitHub Pages | Not supported | Supported (the whole point of `/docs`) |
| cPanel | Supported | Not used |
| Diagrams | None | **21 repo-owned SVGs**, dedicated Diagrams page, grouped TOC, per-diagram verification + uncertainty; metadata hardcoded in HTML, **not** stored as records |
| Provenance / freshness | "Last meaningful update: <date>" (undefined term) | "Last meaningful update"; "Current AI work: completed"; **no source commit / generated timestamp / snapshot label** |
| Initialization | `INITIALIZE.md` (6 steps) | `VIBEKB.md` + `VIBEKB_MAINTENANCE.md` (repo-local, Stoppr-specific) |
| Maintenance | `MAINTENANCE.md` | `VIBEKB_MAINTENANCE.md` (good "Do not" list) |
| Validation | Loader validates in PHP; Reference view surfaces issues | Same loader semantics; no standalone validator |
| Broken-link protection | Unresolved chips flagged (`⚠`) | Same at model level; static links unverified |
| Accessibility | Skip link, focus states, ARIA nav | Same, plus SVG `<title>`/`<desc>` |
| No-JavaScript behaviour | Core nav/content work without JS | Same for reading; search requires JS (acceptable) |
| Generated-output ownership | N/A (nothing generated) | `/docs` generated but **no regeneration path** — one-shot, no script |

---

## 2. Category 1 — Reusable **product** improvements

These change what VibeKB *is for its users*, in ways that serve the promise.

| # | Improvement | StopPR source / pattern | VibeKB target | Why it improves VibeKB | Decision |
|---|---|---|---|---|---|
| P1 | **Static snapshot as a first-class output mode** | `/docs` for GitHub Pages | New Mode B alongside the PHP guide | Lets a vibe coder publish and share an honest read-only guide with zero hosting/PHP | **Adapt** |
| P2 | **Diagrams as a first-class capability** | Diagrams page + 21 SVGs | `.vibekb/diagrams/` model + Diagrams view | A visual map is often the fastest way to understand "what the software is doing" | **Adapt** (as records, not inline HTML) |
| P3 | **Honest provenance instead of vague freshness** | (StopPR *lacked* this — negative evidence) | Shared provenance component | Distinguishing a snapshot from live truth is core to the truth/provenance rules | **Adopt as a fix** |
| P4 | **Consistent counting vocabulary** | (StopPR *worsened* this: "30 areas") | areas vs records vs status counts, validated | Ambiguous totals mislead the exact user VibeKB serves | **Adopt as a fix** |
| P5 | **One-shot snapshot positioning** | "static snapshot suitable for GitHub Pages" | README + provenance language | Sets correct expectations: a snapshot, not an auto-updating oracle | **Adopt** |
| P6 | **Warning-first landmine surfacing** | Overview "Active warnings" callout with severity | Strengthen existing warning display | Surfacing real landmines before an agent edits code is VibeKB's highest-leverage safety feature | **Adapt** |

## 3. Category 2 — Reusable **implementation** improvements

Interface/engineering patterns worth generalizing.

| # | Improvement | StopPR source | VibeKB target | Why | Decision | Compatibility risk |
|---|---|---|---|---|---|---|
| I1 | Diagrams page structure (grouped TOC, "What am I looking at?", per-diagram meta) | `docs/diagrams/index.html` | New `diagrams` template + static page | Clear, teachable visual section | **Adapt** | Metadata must move into records, not HTML |
| I2 | Accessible SVG convention (`role="img"`, `<title>`, `<desc>`, dashed "inferred"/"warn" styling) | `docs/assets/diagrams/*.svg` | Diagram-authoring guidance + example SVGs | Accessibility + honest uncertainty in-diagram | **Adopt** | None |
| I3 | Client-side search over a generated index | `search.json` + `guide.js` | `search.json` + vanilla search | Findability across records | **Adapt** | **Reject jQuery**; rewrite vanilla |
| I4 | Client-side functionality filtering | `#functionality-filters` in `guide.js` | Progressive-enhancement filters (data-attrs) | Filtering works in static mode too | **Adapt** | Must keep no-JS reading intact |
| I5 | Header search box + mobile drawer + skip link | `docs/*/index.html` | Already present in VibeKB; keep, wire search | Consistent chrome across pages | **Adopt (already ours)** | None |
| I6 | Relative asset/link paths (subpath-safe) | `../assets/...` throughout | Static URL strategy | Works under `user.github.io/repo/` | **Adopt** | Dynamic mode must keep query-string routing |
| I7 | `.vibekb/.htaccess` raw-content guard | (VibeKB already has this) | Keep; document that static `/docs` intentionally publishes a rendered subset | Protects raw source under PHP hosting | **Keep** | Static mode has no htaccess; only rendered pages ship |
| I8 | Repo-local VibeKB pointer file | `VIBEKB.md` | Optional `VIBEKB.md` produced by initialization | Orients readers of the target repo | **Adapt** | Keep generic |

## 4. Category 3 — StopPR-specific content that must **not** be copied

Hard exclusions. None of this may enter VibeKB's canonical example or product.

| Item | Why excluded |
|---|---|
| Stoppr functionality records (app-startup, paywall, community, nutrition, panic-flow, …) | Application content for a different app; SousMeow stays canonical |
| Flutter / Dart file references (`lib/main.dart`, `*_cubit.dart`, `home_screen.dart`) | StopPR app internals |
| Firebase / Firestore details (`firestore.rules`, collections, `firebase_options.dart`) | StopPR infrastructure |
| Subscription specifics (Superwall, RevenueCat, placement IDs, quotas) | StopPR business logic |
| StopPR API-key / OAuth / widget-app-group warnings | StopPR's real landmines, not VibeKB's |
| Project name, branding ("Stoppr", version 7.4.2+1), tagline | StopPR identity |
| The 21 Stoppr application diagrams and their SVG contents | App-specific; VibeKB ships a small SousMeow set instead |
| `.env.local`, `firebase.json.local`, `firestore.indexes.json`, any secrets/config values | Secrets; never import (constraint #11) |
| `Makefile`, `analyze_results.txt`, `pubspec.yaml`, `android/`, `ios/`, `lib/`, `assets/` | StopPR application source tree |
| StopPR's shipped `docs/assets/css/guide.css` (79 lines, diagram-only) | Incomplete; VibeKB's own 1083-line design system is authoritative |

## 5. Category 4 — Conflicts requiring an explicit architectural decision

| # | Conflict | Options | **Decision** |
|---|---|---|---|
| C1 | **Renderer:** dynamic PHP guide vs static `/docs` | (a) replace PHP with static; (b) keep only PHP; (c) support both | **(c)** Keep the PHP guide as **Mode A**; add a generator that renders the **same templates** into `/docs` as **Mode B**. One source, one template set, one design system, two URL strategies. StopPR dropped PHP; VibeKB must not, because cPanel/subfolder support is a locked constraint. |
| C2 | **Diagram metadata location:** inline HTML (StopPR) vs records | (a) inline; (b) `.vibekb/diagrams/` records | **(b)** Diagrams become repository-owned records (`.vibekb/diagrams/index.json` + per-diagram front matter) with SVG assets, so both modes render them and validation can check them. Inline HTML is not repository-owned source. |
| C3 | **Search dependency:** jQuery (StopPR) vs vanilla | (a) keep jQuery CDN; (b) vanilla, no CDN | **(b)** Rewrite `guide.js` in vanilla JS and drop the jQuery/Google-Fonts hard dependency. Static output must not require an external CDN for core functionality (and neither should the dynamic guide). |
| C4 | **Freshness claims:** "Last meaningful update" / "completed" | (a) keep; (b) objective provenance | **(b)** Replace with *Source commit analyzed*, *Analysis generated*, *Work-record status*, *Last verified against source*, and a snapshot disclaimer. Never imply auto-freshness (constraint #6). |
| C5 | **Regeneration path:** one-shot (StopPR) vs maintainable | (a) hand-generate once; (b) repo-owned script | **(b)** Ship `tools/generate-static.php` (PHP-only, no external deps) as the documented, reproducible way to build/refresh `/docs`. StopPR's missing regeneration path is a defect to fix, not a pattern to copy. |
| C6 | **Count unit** on Overview | "N areas" vs "N records across M areas" | Show **"N functionality records across M functional areas"** plus status counts; add validation so contradictory totals are hard to publish. This fixes a bug present in **both** repos. |
| C7 | **Where generated output lives / who owns it** | mix source and output vs separate | `/docs` is **generated output**, not source of truth; each page carries a visible generated-output notice, and `.vibekb/` remains the single source. |

---

## 6. Net decision summary

**Port (adapted):** static `/docs` output mode; a maintainable PHP generator;
a `.vibekb/diagrams/` records model with a small accurate SousMeow SVG set; a
shared provenance component; client-side search + filters in vanilla JS; a
Diagrams nav item; the diagrams-page teaching structure; subpath-safe relative
links.

**Fix (bugs found in both):** the "areas" vs "records" count conflation; the
undefined "Last meaningful update" / "Current AI work: completed" labels; the
external-CDN hard dependency (jQuery, Google Fonts).

**Reject:** all Stoppr application content, files, secrets, branding, and the
21 app-specific diagrams; StopPR's incomplete shipped CSS; StopPR's one-shot
no-regeneration approach; dropping the PHP guide.

**Preserve unchanged:** the product definition and promise; the SousMeow
canonical example (updated only to demonstrate new capabilities); PHP 8.2
shared-hosting + subfolder deployment; the `.vibekb/.htaccess` guard; every
truth/provenance rule.
