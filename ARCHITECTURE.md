# ARCHITECTURE.md — VibeKB's architecture and its evolution into a developer CLI

This document is the architectural assessment for VibeKB and the decision that
came out of it: **VibeKB evolves a Go developer CLI as its front-end while PHP
remains the single canonical implementation of the model core and the deployment
runtime.** It records what the repository is today, why this direction was chosen
over the alternatives, what it preserves, and the staged roadmap. Phase 1 of that
roadmap is implemented in this repository.

> One-line summary: **Go becomes the developer's entry point; PHP stays the
> product's engine. There is one model loader, one template system, one source of
> truth — and now one professional command in front of them.**

---

## 1. The assessment — what VibeKB is today

VibeKB is a PHP 8.2 application with no Composer, no Node, no database, no
external API, and no build step. It runs on cPanel/shared hosting, deploys into a
subfolder, and works without JavaScript. Those properties are locked (see
`CLAUDE.md`, `PRODUCT.md`) and every decision below protects them.

Its PHP responsibilities fall into four groups. Mapping them is the whole point of
the assessment, because the right architecture follows from *which* responsibility
each file carries — not from a language preference.

### 1.1 Filesystem / CLI work (developer-facing tooling)

Deterministic operations over files and git. No web request, no HTML.

| File | Responsibility |
|------|----------------|
| `install.php` | Copy the VibeKB runtime payload into a target repo; scaffold a fresh `.vibekb/`. Reads `template/manifest.json`; walks and copies directories. |
| `tools/vibekb.php` | The self-maintenance CLI: `status`, `check`, `affected`, `bootstrap`, `validate`, `generate`. Uses git (`shell_exec`) and the filesystem. |
| `tools/lib/Starter.php` | The single definition of a fresh, empty `.vibekb/` workspace (directories + starter file contents). Shared by the installer and `bootstrap`. |
| `tools/validate.php` | Headless model validator (CI gate). |
| `tools/test-topology.php` | Topology parser test. |

The commands themselves are thin. Their *real* work — the part with all the
subtlety — is model parsing, which lives in the shared core below.

### 1.2 Web presentation (the deployment runtime)

| File | Responsibility |
|------|----------------|
| `guide/index.php` | The dynamic guide's router (Mode A). |
| `guide/templates/*.php` | The one and only template set. |
| `guide/assets/*` | CSS/JS for the guide. |
| `index.php`, `assets/` | The marketing homepage (VibeKB-repo-only). |

This is what a cPanel host serves. It must remain PHP.

### 1.3 Shared logic — the model core (the crux)

| File | Responsibility |
|------|----------------|
| `guide/lib/Content.php` (~1,240 lines) | Loads `.vibekb/`, parses front matter, resolves relationships, validates, and exposes a ~40-method read API over the model. |
| `guide/lib/FrontMatter.php`, `Markdown.php`, `Provenance.php` | Parsing and provenance primitives. |
| `guide/lib/UrlStrategy.php`, `nav.php`, `map.php`, `search.php`, `helpers.php` | Rendering-adjacent helpers and the search index. |

**This is the centre of gravity.** Both the CLI tools and the web runtime depend
on `guide/lib` — `validate.php`, `generate-static.php`, and `tools/vibekb.php` all
`require` it. The valuable, hard, drift-sensitive logic is here, not in any one
command.

### 1.4 Generated output and installer payload

`/docs` (a render of the model, produced by `generate`, never hand-edited),
`template/` (the payload manifest), and `examples/` (fixtures). Language-neutral
by nature.

### 1.5 The six questions, answered

1. **Which responsibilities are filesystem/CLI work?** Installation, workspace
   scaffolding, drift detection (git diff + path existence), and command
   dispatch. (§1.1)
2. **Which are web presentation?** The dynamic guide, the templates, the assets,
   the homepage. (§1.2)
3. **Which are shared logic?** The model loader/validator and its parsing and
   provenance primitives — `guide/lib`. (§1.3)
4. **Which should never depend on PHP?** The *developer's entry point and
   distribution* — installing and invoking VibeKB shouldn't require first
   installing a language runtime. Also the model *format* itself (`.vibekb/`),
   which is data and must stay language-independent.
5. **Which should remain PHP?** The deployment runtime (Mode A) **and** the model
   core (§1.3). The core stays PHP not because PHP is ideal for it, but because it
   already exists, is verified, and must have exactly **one** implementation.
6. **Which would benefit from Go?** The developer front-end: a single static
   binary (no runtime to install to *try* VibeKB), fast native filesystem/git
   work, cross-platform packaging (brew/winget/curl), and genuinely new
   diagnostics (`doctor`).

---

## 2. The core finding

> The commands are cheap. The **model loader is expensive.** Porting
> `check`/`validate`/`status` to Go does not port a command — it ports
> `Content.php`, because that is where their behaviour actually lives.

VibeKB's identity is *resisting drift* and *honest provenance*: "the model is the
API." A second implementation of the model loader — one in Go, one in PHP — is the
single most dangerous thing that could be added to this codebase. The day the Go
validator and the PHP validator disagree about whether the model is valid, the
product's core promise breaks, and the failure is silent.

So the architecture is chosen around one rule: **never fork the model core.**

---

## 3. The decision

**Adopt a Go developer CLI as a portable front-end that delegates every
model-semantic operation to the one canonical PHP core.** Go owns distribution,
UX, native diagnostics, and the deterministic filesystem/git work that does not
depend on model semantics. PHP owns the model loader, the guide runtime, and
static generation — unchanged.

Three architectures were weighed:

- **A. Status quo + packaging only.** Cheapest, but leaves the real DX gap: you
  must install PHP just to *try* VibeKB's tooling, and there is no
  `brew install vibekb` story. Rejected as insufficient.
- **B. Full Go port; PHP as runtime only.** Maximum "Go", maximum distribution
  polish — and it forks the model loader into two languages. This directly
  attacks VibeKB's anti-drift purpose (§2). **Rejected**, and this is the "do not
  replace PHP simply because Go exists" case the brief warns against.
- **C. Go front-end, PHP core (chosen).** Delivers the single-binary DX and the
  `brew`/`winget`/`curl` path, keeps one model loader and one template system,
  and is purely additive — nothing that works today changes. It maps exactly onto
  the `runtime/php/` idea in the brief: PHP is the runtime the front-end drives.

This is not "Go vs PHP." It is **Go where a portable binary and native UX help,
PHP where a single verified engine must not be duplicated.**

---

## 4. What this preserves

Every locked strength is untouched, because the runtime and the core are the same
PHP as before:

- PHP 8.2 only, no Composer, no Node, no database, no API, no build step **for the
  runtime**. (The Go binary is a *developer convenience* built separately; the
  cPanel host never sees Go.)
- Works on cPanel, deploys in a subfolder, usable without JavaScript.
- Self-hosted `.vibekb/`, the dynamic guide (Mode A), and the generated static
  snapshot (Mode B) — all unchanged, one template system, honest provenance.
- The `.vibekb/` model stays language-independent. **The model is the API;
  implementations may change, the model does not.**

The Go CLI adds a dependency only for the *developer* who chooses the binary, and
`vibekb doctor` reports that dependency honestly rather than hiding it.

---

## 5. Command-by-command verdict

| Command | Verdict | Why |
|---------|---------|-----|
| `install` | **Native Go (done).** | Copying files and scaffolding need no model parsing. The installer now runs entirely in Go from an embedded payload — no PHP, no live clone — with the starter definition extracted to `template/starter/` data that both Go and PHP read. See §5a. |
| `bootstrap` | **Keep PHP; front with `vibekb bootstrap`.** | Now reads the same `template/starter/` data the installer embeds. One definition, no drift. (A native Go `bootstrap` is straightforward later, since the definition is already shared data.) |
| `check` | **Keep PHP core; front with `vibekb check`.** | Its drift half is pure git+fs (Go-friendly), but its validation and `/docs`-sync halves run the model loader and the generator. Splitting it would fork the loader. Delegated whole. |
| `generate` | **Keep PHP — strongly.** | Mode B exists specifically so there is **no second template system**: it renders the *same* templates as Mode A. Generating HTML from Go would reintroduce exactly the duplication VibeKB was built to avoid. This is the clearest "stay PHP". |
| `status` / `validate` / `affected` | **Keep PHP core; front in Go.** | Thin wrappers over `Content.php`. |
| `doctor` (new) | **Native Go.** | Environment truth (PHP present? ≥ 8.2? git? workspace?) needs no model core and is most useful *before* PHP exists. |
| `version` / `help` (new) | **Native Go.** | Pure UX. |

The pattern: **native Go for anything that must work without the model core;
delegate everything that touches the model.**

---

## 6. Target architecture

```
VibeKB/
  cmd/vibekb/            # Go entry point (main)
  embed.go               # module-root embed of the installer payload + starter
  internal/
    cli/                 # command surface: dispatch, help, doctor, version
    installer/           # native install: manifest parse, copy, scaffold, verify
    phpcore/             # discover PHP + repo root; delegate to the PHP core
    buildinfo/           # version string (set at link time on release)
  go.mod

  # --- the canonical PHP core + runtime (unchanged) ---
  guide/                 # dynamic guide (Mode A) + guide/lib (the model core)
  tools/                 # vibekb.php, generate-static.php, validate.php, Starter.php
  install.php            # compatibility wrapper → forwards to `vibekb install`
  template/              # installer payload manifest + starter/ (canonical data)
  .vibekb/               # the self-hosted living model (the product)
  docs/                  # generated Mode B snapshot
```

Directories the brief sketched (`internal/validator`, `internal/generator`,
`internal/model`, …) are **intentionally not created empty**. They appear only if
and when a phase actually needs native logic there — creating them speculatively
would be the kind of premature structure the brief cautions against. `runtime/php`
is reserved (§8) for a future *bundled* PHP, not created until that phase is real.

A larger, optional restructuring — lifting the model core out of `guide/lib` into
a shared top-level location both the guide and the tools import — is **deferred to
Phase 3** (§7). It is a genuine improvement (the tools currently depend on a
`guide/` path, which reads backwards), but it touches deployment paths, the
generator, and the installer payload, so it is not worth the risk until the Go
layer is established. Avoiding that rewrite now is deliberate.

---

## 7. Roadmap

Incremental, backwards-compatible, no big-bang rewrite. `php tools/vibekb.php …`
keeps working at every phase.

- **Phase 1 — Go front-end (done).** `cmd/vibekb` + `internal/*`: native
  `version`, `doctor`, `help`; delegation for `status`, `check`, `affected`,
  `bootstrap`, `validate`, `generate`. Repo-root and PHP discovery, honest
  missing-runtime errors, exit-code propagation. CI builds, vets, and tests it.
- **Phase 1b — native installation (done, this change).** `vibekb install` is
  fully native: it embeds the payload and a canonical starter definition
  (`template/starter/`) and installs with **no PHP** and no live clone. The
  starter model was extracted from PHP code into shared data that both the Go
  installer and PHP `bootstrap` read (pulling the starter-as-data item forward
  from Phase 3). `install.php` became a compatibility wrapper. This is what makes
  the single-binary install path real.
- **Phase 2 — distribution.** Release automation (cross-compiled binaries,
  checksums), a `curl | sh` installer, a Homebrew tap, and a winget manifest, so
  `brew install vibekb` / `winget install vibekb` become real. Unblocked now that
  install is self-contained.
- **Phase 3 — shared core boundary.** Extract the model core out of `guide/lib`
  into a shared location imported by both the guide and the tools. (The starter
  payload is already data, done in Phase 1b.) This removes the "tools depend on
  guide" inversion. Still one loader.
- **Phase 4 — evaluate a bundled runtime.** Only if the "developers shouldn't
  think about PHP at all" goal demands it, evaluate shipping PHP *with* the binary
  (e.g. FrankenPHP / static-php-cli) so `vibekb` needs nothing preinstalled. This
  is explicitly a later decision with real trade-offs (binary size, security
  updates, platform matrix) — not assumed.

Replacing the PHP tooling with native Go is **not** a roadmap goal. Each phase is
justified on its own; none requires forking the model core.

---

## 8. Distribution & the honest PHP dependency

The end-state DX is:

```
vibekb install        # into your repo — native, no PHP required
vibekb check          # verify the model  (delegates to PHP)
vibekb generate       # publish /docs     (delegates to PHP)
# deploy the PHP guide to any host (cPanel, static Pages, …)
```

`install`, `doctor`, and `version` are native and need **no PHP** — installation
is now a self-contained binary step (Phase 1b). The *model* commands (`check`,
`generate`, `status`, …) still delegate to PHP 8.2+, and that is stated plainly:
`vibekb doctor` reports whether PHP is present rather than pretending it isn't.
Overclaiming "no PHP anywhere" would violate VibeKB's own honesty rules. Phase 2
(distribution) makes the binary trivial to get; Phase 4 (an optional bundled
runtime) is what would eventually make even the model commands PHP-free.

---

## 9. Backwards compatibility

- `php tools/vibekb.php <cmd>` is unchanged and remains supported. `php
  install.php` still works too — it is now a thin wrapper that forwards to `vibekb
  install`, so there is one installer, not two.
- The model core is untouched: the loader, guide, and generator changed no
  behaviour. `tools/lib/Starter.php` was refactored to *read* the extracted
  `template/starter/` data, producing byte-identical scaffolding (verified).
- The Go source is developer/CI tooling only. It is excluded from the cPanel
  deployment (`.cpanel.yml`) and from the installer payload
  (`template/manifest.json` → `not_installed`). The starter *definition*
  (`template/starter/`) **is** installed into targets, so `bootstrap` keeps
  working there; a repository that adopts VibeKB still receives the PHP runtime
  exactly as before.

See `INSTALLER.md` and `MAINTENANCE.md` for the developer workflow, and
`DEPLOYMENT.md` for why the Go CLI is not deployed.
