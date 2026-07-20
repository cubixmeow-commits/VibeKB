# BUILD_REPORT.md — VibeKB V1 foundation

## Initial repository assessment

The repository already contained a PHP site, but it was built around a **drifted
product**: two engines — a "Project Guide" presentation (`guide/`) and a
technical-reference "edition" (`edition/`) — both organized around **repository
memory / "preserving understanding."** The `.vibekb/` content was structured by
memory type (decisions, risks, warnings, timeline, sessions, glossary…), and the
homepage led with "code is generated faster than humans can understand it →
preserve understanding."

Reusable foundation found: a clean `FrontMatter` + `Markdown` + content-loader
pattern, the SaaS Idea Manager example subject, and the cPanel deploy model.

## Product drift found and corrected

- **Framing:** "repository-memory / documentation companion" → **"understand
  what your software is currently doing,"** with functionality as the primary
  unit and memory demoted to a supporting role.
- **Content organization:** by memory type → **by functionality**, with memory
  linked back to functionality.
- **Homepage:** a large multi-section marketing page for the old framing →
  a **minimal, honest** statement of the locked product that links into V1.
- Removed the drifted engines and old memory-only content so nothing contradicts
  the locked definition (acceptance criterion #24).

## Files created

- **Product docs:** `PRODUCT.md`, `CLAUDE.md`, `SCHEMA.md`, `MAINTENANCE.md`,
  `INITIALIZE.md`, `BUILD_REPORT.md`. Rewrote `README.md`, `AGENTS.md`,
  `DEPLOYMENT.md`.
- **Guide app (`guide/`):** `index.php` (front controller); `lib/FrontMatter.php`,
  `lib/Markdown.php`, `lib/helpers.php`, `lib/Content.php`; 12 templates
  (`layout`, `overview`, `functionality-index`, `functionality-detail`,
  `how-it-works`, `data`, `files`, `current-work`, `changes`, `why`, `handoff`,
  `reference`, `not-found`); `assets/css/guide.css`, `assets/js/guide.js`.
- **Homepage:** rewrote `index.php`; added `assets/css/home.css`.
- **Content model (`.vibekb/`):** `manifest.json`; `project/` (4);
  `functionality/index.json` + 6 records; `system/` (6); `files/important-files.json`;
  `memory/` (2 decisions, 3 constraints, 2 assumptions, 3 warnings, 1 discovery,
  1 change); `work/` (current, handoff, 1 session).

## Files changed / removed

- **Removed:** `edition/`; old `guide/` engine; old homepage assets
  (`homepage.css/js`, `landing.css`, `demo-home.png`); `docs/`; old `.vibekb/`
  memory-type directories and JSON manifests (kept `.vibekb/.htaccess`).
- **Updated:** `.cpanel.yml` (runtime paths, doc excludes).

## Architecture chosen

A single PHP front controller (`guide/index.php`) with **query-string routing**
(`?view=…`), so no rewrite rules are needed and it works in a subfolder. A
reusable content layer (`Content.php`) loads, resolves relationships, and
validates; templates are thin and share a layout. File access is confined to
`.vibekb/`; all output is escaped.

## Content format chosen

Markdown with front matter for records (human-readable, AI-editable, stable ids,
relationships) + small JSON manifests for indexes. No database, no build step.
The front-matter parser supports scalars, inline/block lists, so relationships
live in front matter and prose lives in the body.

## Views implemented

Software Overview · Functionality Index (server-side filters) · Functionality
Detail (narrative + resolved relationship rails) · How It Works · Data & Storage
· Files That Matter · Current AI Work · Changes · Why It Works This Way · AI
Handoff · Reference (record model + live validation diagnostics).

## Sample records created

6 functionality records covering all required behaviour kinds: user-created data
(`create-idea`), retrieval (`browse-ideas`, `view-idea`), modification
(`change-idea-status`), system (`initialize-database`), and output
(`export-ideas`). One primary user workflow and one system workflow, important
files, storage, current AI work, one change, decisions, constraints, assumptions
(with differing verification states), warnings, a discovery, and a handoff — all
cross-linked.

## Validation implemented

The loader detects: duplicate ids, missing required fields, out-of-vocabulary
status/verification/safety values, broken `depends_on`/`related_memory`/back-link
relationships, malformed JSON, and unreadable files. Results render in the
Reference view; a dev-only banner links to them.

## Tests performed

- `php -l` on every PHP file — clean.
- Started the built-in server; all 10 views + functionality detail returned 200;
  unknown view and unknown functionality id returned 404.
- Reference view: **no validation errors** with the seed content.
- Injected a malformed record → loader surfaced the issue and stayed up (no
  crash); removed after.
- Output escaping confirmed (record content is escaped before rendering).
- Subfolder-safe URLs confirmed (links derive from `SCRIPT_NAME`).
- Single `<h1>` per page; assets load 200; homepage states the product line and
  links into the guide.

## Tests not possible in this environment

- Real iPhone / touch rendering (CSS is responsive and mobile-first, but not
  device-tested).
- Apache `.htaccess` deny-access behaviour for `.vibekb/` (the PHP built-in
  server ignores `.htaccess`; verified the file is present and correct).
- A live cPanel deploy.

## Limitations

- The example app's source files are described, not shipped.
- Extraction is manual (agent-maintained), by design for V1.
- The Markdown renderer is a pragmatic subset, not full CommonMark.

## Recommended next task

**Redesign the VibeKB homepage and final example experience around the working
V1 product** — now that the product model is proven, build the marketing
homepage, the polished showcase example, and the onboarding experience on top of
this foundation.
