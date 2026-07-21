---
id: handoff
type: handoff
title: Current handoff
summary: Explainable Diagrams shipped in V1 — two SousMeow diagrams now carry a repository-owned topology (nodes, edges, mechanisms, files-with-reasons, source links) rendered identically in both output modes and usable without JavaScript. Next, upgrade the remaining three diagrams and trace the still-inferred SousMeow areas.
updated: 2026-07-21
verification_state: mixed
---

## What the software (VibeKB) now does

VibeKB renders one `.vibekb/` source through one template set in two modes: the
dynamic PHP guide (Mode A) and a static `/docs` snapshot (Mode B, via
`tools/generate-static.php`). Diagrams are first-class source-grounded records
and can now be **explainable**: an optional per-diagram topology
(`.vibekb/diagrams/topology/<id>.json`) gives every node a plain-language
purpose, every edge a concrete mechanism from a controlled vocabulary and a
one-sentence explanation, every displayed file a role and a reason, a compact
repository location, and a commit-pinned external source link as the terminal
"show me the implementation". Every rendering shows objective provenance and
never implies auto-freshness; counts distinguish areas from records; search and
filters run client-side with no jQuery and no required CDN.

## Explainable Diagrams — what was built this pass

- **Schema + vocabulary:** JSON topology (nodes/edges) referenced by a diagram
  record's `topology:` field. Single canonical `edge_mechanism_vocabulary()` and
  `file_role_vocabulary()` in `guide/lib/helpers.php`; two edge states —
  verified (solid) and inferred (dashed), both stated in text.
- **Loader + resolution:** `Content.php` loads/normalises topology, resolves
  node/edge functionality and warnings, reuses `important-files.json` metadata,
  and builds immutable GitHub source links from manifest provenance.
- **Validation:** the loader + `tools/validate.php` enforce the full contract
  (unique ids, resolvable edges, controlled mechanisms, honest verification,
  files-with-reasons, SVG↔topology marker mapping both ways). New
  `tools/test-topology.php` proves malformed topology produces useful
  diagnostics without crashing.
- **Rendering:** semantic no-JS node/edge explanation sections
  (`guide/templates/partials/diagram-explain.php`) with `#node-<id>` /
  `#edge-<id>` anchors; SVGs mark groups with `data-vibekb-node` /
  `data-vibekb-edge` linking to those anchors. Small vanilla-JS enhancement adds
  selection, dimming, and focus (`initDiagrams` in `guide/assets/js/guide.js`);
  nothing essential is behind JS.
- **Search + backlinks:** node titles/purposes, edge labels/explanations, and
  files-with-reasons are searchable and link to the exact anchor; functionality
  pages deep-link to the node that represents them.
- **Examples:** upgraded **request-flow** (verified web path + two inferred,
  dashed edges: the Router match and the CLI write path) and **run-recipe-flow**
  (verified user journey with the untrusted-paste and read/write-coupling
  warnings). The other three diagrams remain valid as picture + narrative.

## SousMeow functionality state (unchanged this pass)

- **Verified from source:** discovery, auth, the full Runner
  (run-recipe → build-prompt → paste-response → review-quality-checks →
  approve-and-version), export-project-kit, admin overview, routing/security,
  database access.
- **Partial:** `reset-password` (SMTP-dependent web flow); `demo-simulation`
  (paste-example verified; bulk inferred).
- **Inferred from source:** `manage-account`, `seed-and-sync-content`, the
  Router, and some Model queries. No verification state was upgraded for the
  diagrams — inferred edges are drawn dashed and state their basis.

## Provenance note (important)

The SousMeow model was derived read-only and is not bundled into VibeKB. It can
go stale — re-verify against the SousMeow source
(`cubixmeow-commits/dev-portfolio-v2`, `projects/sousmeow`, commit `c1617ab`)
before changing any functionality claim. Source links are pinned to that commit.

## Verification completed this pass

- `php -l` clean across changed `guide/` and `tools/` files.
- `php tools/validate.php` → 0 errors (25 records, 9 areas, 5 diagrams, 2
  explainable topologies, 13 nodes, 11 edges); warnings are only the three
  not-yet-explainable diagrams.
- `php tools/test-topology.php` → OK (all malformed-topology diagnostics fire;
  good topology still resolves).
- Dynamic guide: diagrams + functionality views return 200; source links are
  commit-pinned; search index exposes node/edge/file entries with correct
  anchors.
- Static `/docs` regenerated: node/edge anchors, relative source links, and
  functionality backlinks resolve.

## Active warnings (SousMeow)

- `read-write-path-coupling`, `pasted-response-is-untrusted`,
  `password-reset-depends-on-smtp`, `legacy-category-column`.

## Exact next recommended action

Author topology for the remaining three diagrams (`app-overview`, `storage-map`,
`risk-uncertainty-map`) — each currently renders as picture + narrative and is
flagged by a non-fatal validator warning — applying the same explainability
gate. In parallel, trace `app/Controllers/AccountController.php`,
`scripts/seed.php`, and the `Simulation*` services in the SousMeow source to
promote the inferred edges/records (Router match, CLI write path,
`manage-account`, `seed-and-sync-content`) to verified where the source
supports it. Then re-run `php tools/validate.php`, `php tools/test-topology.php`,
and `php tools/generate-static.php`.
