# SCHEMA.md — VibeKB content model

All VibeKB content lives in `.vibekb/` as human-readable, AI-editable files:
Markdown with front matter for records, and small JSON manifests for indexes.
No database is required. The loader (`guide/lib/Content.php`) parses, resolves
relationships, and validates this content; the Reference view shows live
diagnostics.

## Directory layout

```
.vibekb/
├── manifest.json                 # content-model version + example metadata
├── project/
│   ├── identity.md               # what the software is
│   ├── intent.md                 # why it exists / what it must not become
│   ├── current-state.md          # what it does right now
│   └── constraints.md            # boundaries overview
├── functionality/
│   ├── index.json                # group definitions + display order
│   └── records/*.md              # one file per functionality (the primary unit)
├── system/
│   ├── mental-model.md
│   ├── components.md
│   ├── request-flow.md
│   ├── data-flow.md
│   ├── storage.md
│   └── deployment.md
├── files/
│   └── important-files.json      # curated important files
├── diagrams/
│   ├── index.json                # diagram group definitions + display order
│   ├── records/*.md              # one file per diagram (record + metadata)
│   └── assets/*.svg              # repository-owned SVGs (accessible title+desc)
├── memory/
│   ├── decisions/*.md
│   ├── constraints/*.md
│   ├── assumptions/*.md
│   ├── warnings/*.md
│   ├── discoveries/*.md
│   └── changes/*.md
└── work/
    ├── current.md                # current AI work
    ├── handoff.md                # current handoff
    └── sessions/*.md             # AI work sessions
```

## Front matter conventions

Records are Markdown files that begin with a `---` fenced front-matter block.
Supported value forms: scalars (`key: value`), quoted strings, booleans,
integers, inline lists (`key: [a, b, c]`), and block lists (`- item` on the
following lines). The Markdown body holds the human narrative.

## Shared base fields

| Field | Meaning |
|-------|---------|
| `id` | Stable, unique id (lowercase, digits, hyphens). Defaults to filename. |
| `type` | Record type (see below). Defaults from the directory. |
| `title` | Human title. |
| `summary` | One-sentence plain-language summary. |
| `status` | Lifecycle/verification status (per type). |
| `verification` | Provenance state (see below). |
| `created`, `updated` | ISO dates. |
| `tags` | List of tags. |
| `functionality` | List of functionality ids this record relates to. |
| `files` | List of file paths involved. |

## Record types

### functionality (`functionality/records/*.md`) — the primary unit
Front matter: `id, title, summary, area, status, verification, user_facing,
trigger, files, reads, writes, config, depends_on, related_memory, created,
updated, tags`. Body sections (A–Q): In one sentence · User experience ·
Current behavior · Step-by-step flow · Implementation map · Data used ·
Dependencies · Dependents · Failure cases · Configuration · Current state ·
Safe to change · Use caution · Why it works this way · Change history · Current
AI work · Related functionality.

- `area` must match a group id in `functionality/index.json` (unknown areas
  fall into an "Other" group).
- `depends_on` lists other functionality ids; **dependents are derived**
  (reverse links) automatically.
- `related_memory` uses `type:id` references, e.g. `decision:never-calls-ai`.

### project (`project/*.md`)
Identity, intent, current-state, constraints documents. Free-form body with a
`summary` in front matter.

### system (`system/*.md`)
Mental model, components, flows, storage, deployment. `title`, `summary`, body.

### file (`files/important-files.json`)
JSON objects: `path, purpose, functionality[], runs_when, depended_on_by[],
depends_on[], safety, test_after_change, provenance`.

### diagram (`diagrams/records/*.md`)
Source-grounded SVG maps of how the software works. Front matter: `id, title,
summary, diagram_type, group, svg, functionality[], files[], data[],
warnings[], diagrams[], status, verification, provenance, last_verified,
uncertainty, created, updated`. The Markdown body explains what the viewer is
seeing ("What am I looking at?", "Why it matters", "What is uncertain").

- `svg` is a filename in `diagrams/assets/`; the SVG **must** be well-formed XML
  with an accessible `<title>` and `<desc>`, and is rendered inline.
- `group` must match a group id in `diagrams/index.json` (unknown groups fall
  into an "Other" group).
- `functionality[]`, `warnings[]`, and `diagrams[]` are back-links resolved and
  validated against functionality records, memory warnings, and other diagrams.
- `diagram_type` is one of the supported types (application-overview,
  user-journey, startup-flow, authentication-flow, access-flow, navigation-map,
  feature-access, request-flow, data-flow, storage-map, external-services,
  code-architecture, state-management, risk-and-uncertainty-map). These are
  *supported* types, not mandatory diagrams — a repository ships only the
  diagrams it can ground in source, and must label inferred or unverified paths
  in the diagram itself.

### memory records (`memory/<type>/*.md`)
`decision, constraint, assumption, warning, discovery, change`. Each links back
via `functionality` and/or `files`. Type-specific fields:
- **decision**: `alternatives`, plus body Context/Decision/Reason/Consequences.
- **constraint**: `status` (active), source and consequences in body.
- **assumption**: `confidence`, `verification`, `invalidated_by`, `next_check`.
- **warning**: `severity` (critical/high/medium/low).
- **discovery**: evidence + `changed_model` in body/front matter.
- **change**: before/after/impact in body; may link a `session`.

### work (`work/*.md`)
- **current** (`current.md`): `objective, status, verification_state,
  affected_functionality, expected_files, data_impact, risks` + body.
- **handoff** (`handoff.md`): `verification_state` + body.
- **session** (`sessions/*.md`): `date, verification, functionality, files,
  change` + body.

## Controlled vocabularies

**Statuses** (functionality): `implemented`, `partial`, `planned`,
`experimental`, `disabled`, `deprecated`, `broken`, `unknown`,
`needs-verification`.

**Verification / provenance**: `verified-by-test`, `verified-manually`,
`verified-from-source`, `inferred-from-source`, `reported-by-developer`,
`not-verified`, `verification-failed`, `superseded`, `contradicted`.

**File safety levels**: `presentation-only`, `low-impact`, `moderate-impact`,
`understand-dependencies-first`, `high-impact`, `generated-or-managed`,
`unknown`.

**Warning severity**: `critical`, `high`, `medium`, `low`.

## Manifest provenance & generation metadata

`manifest.json` carries a `provenance` block describing the **source** the guide
explains — `name`, `source_repository`, `source_subpath`, `source_branch`,
`source_commit`, `analyzed`, `verification_scope`, `last_verified`,
`updates_automatically` (always `false` unless an actual update mechanism is
implemented and verified), and a `freshness_note`.

The **generation** event (mode, generated time, generator commit/branch) is
supplied at render time — `dynamic` for the live PHP guide, `static` for a
`/docs` snapshot — and is never conflated with the source provenance. The guide
renders both through the shared provenance component (`guide/lib/Provenance.php`)
using objective labels (*Source commit analyzed*, *Analysis generated*,
*Work-record status*, *Last verified against source*), never undefined ones like
"Last meaningful update".

## Count vocabulary

Three counts must never be conflated, and every displayed total states its unit:
**functional areas** (grouped categories), **functionality records** (individual
behaviours), and **status counts** (records by status). Totals are derived from
the records themselves so they cannot silently contradict each other.

## Validation rules (enforced by the loader and `tools/validate.php`)

- Duplicate `id`s within a type are reported (functionality and diagrams).
- Functionality must have `id`, `title`, `status`, `summary`.
- `status`, `verification`, and file `safety` must be in the vocabularies.
- `depends_on` must point to existing functionality.
- `related_memory` and memory `functionality` back-links must resolve.
- File `functionality` links must resolve.
- Diagrams must have `id`, `title`, `svg`; a valid `verification`; an SVG asset
  that exists, is well-formed XML, and has an accessible `<title>` (and
  ideally `<desc>`); and resolvable `functionality`, `warnings`, and `diagrams`
  links.
- `tools/validate.php` additionally checks provenance completeness, that
  area/record/status totals reconcile, and — when a `/docs` snapshot exists —
  that its search entries point at pages that exist. It exits non-zero on error.
- Malformed JSON and unreadable files are reported, not fatal.

Issues appear in the **Reference** view; in development a banner links to them.
`php tools/validate.php` reports the same set headlessly for CI.
