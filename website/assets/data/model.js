/*
 * model.js: the data behind the Live Repository Map.
 *
 * Every value here is transcribed from VibeKB's own `.vibekb/` model
 * (functionality/index.json, functionality/records/*.md, and the explainable
 * topology under .vibekb/diagrams/topology/). It is real repository knowledge,
 * not invented marketing content. If the model changes, this file is refreshed
 * from it, the same discipline the product itself follows.
 *
 * Exposed as a plain global so the map works with no build step and no modules.
 */
window.VIBEKB_MODEL = {
  meta: {
    app: "VibeKB",
    outcome: "Understand what your software is doing.",
    // Objective provenance, mirrored from `php tools/vibekb.php status`.
    sourceCommit: "fd08afa",
    analyzed: "2026-07-23",
    updatesItself: false,
    guideBase: "https://iainreid.dev/vibekb/guide/",
  },

  // Live, honest totals from VibeKB's own model.
  stats: [
    { key: "functionality", label: "functionalities", value: 23 },
    { key: "areas", label: "functional areas", value: 8 },
    { key: "systems", label: "systems", value: 6 },
    { key: "files", label: "files that matter", value: 29 },
    { key: "relationships", label: "relationships", value: 45 },
    { key: "diagrams", label: "explainable diagrams", value: 3 },
  ],

  // Level 1: functional areas (the primary map nodes). `hub: true` marks the
  // area the rest of the model leans on most (living-model), so the layout can
  // seat it near the centre.
  areas: [
    {
      id: "living-model",
      title: "The living model",
      blurb:
        "How the repository-owned .vibekb/ content is loaded, parsed, relationship-resolved, and validated into the model everything else renders.",
      hub: true,
      capabilities: [
        {
          id: "load-living-model",
          title: "Load the living model",
          summary:
            "A single loader reads the .vibekb/ directory into an in-memory model, with all filesystem access confined to the content root.",
          status: "implemented",
          verification: "verified-from-source",
          files: ["guide/lib/Content.php", "guide/lib/FrontMatter.php", "guide/lib/Markdown.php"],
        },
        {
          id: "parse-records",
          title: "Parse records (front matter + Markdown)",
          summary:
            "A small front-matter parser and a pragmatic Markdown subset turn each file into structured metadata plus HTML, with no Markdown library and no build step.",
          status: "implemented",
          verification: "verified-from-source",
          files: ["guide/lib/FrontMatter.php", "guide/lib/Markdown.php"],
        },
        {
          id: "resolve-relationships",
          title: "Resolve relationships between records",
          summary:
            "The loader turns a flat set of records into a graph: dependencies, memory back-links, file-to-functionality links, and diagram cross-links.",
          status: "implemented",
          verification: "verified-from-source",
          files: ["guide/lib/Content.php"],
        },
        {
          id: "validate-model",
          title: "Validate the model",
          summary:
            "The loader and headless tools/validate.php enforce the content contract (required fields, controlled vocabularies, resolvable references) gating generation and CI.",
          status: "implemented",
          verification: "verified-from-source",
          files: ["guide/lib/Content.php", "tools/validate.php", "guide/templates/reference.php"],
        },
      ],
    },
    {
      id: "agent-workflow",
      title: "AI coding workflow",
      blurb:
        "How a coding agent starts a session, finds what a change affects, detects drift, records active work, and hands off: the self-maintenance lifecycle.",
      capabilities: [
        {
          id: "start-work-session",
          title: "Start a work session",
          summary:
            "php tools/vibekb.php status prints the one screen an agent needs to begin: provenance, current work, the handoff's next action, and a validation + drift summary.",
          status: "implemented",
          verification: "verified-manually",
          files: ["tools/vibekb.php"],
        },
        {
          id: "detect-drift",
          title: "Detect drift between code and model",
          summary:
            "php tools/vibekb.php check reports where model and repository diverged: broken file references, changed source, likely-affected functionality, stale /docs.",
          status: "implemented",
          verification: "verified-manually",
          files: ["tools/vibekb.php", "tools/validate.php", "tools/generate-static.php"],
        },
        {
          id: "find-affected-functionality",
          title: "Find affected functionality",
          summary:
            "Given a set of changed files, php tools/vibekb.php affected turns 'six files changed' into 'here is the knowledge that may now be wrong.'",
          status: "implemented",
          verification: "verified-manually",
          files: ["tools/vibekb.php", "guide/lib/Content.php"],
        },
        {
          id: "record-current-work",
          title: "Record current AI work",
          summary:
            ".vibekb/work/current.md holds the active change (outcome, current vs proposed behaviour, affected functionality, risks, verification plan) rendered as a view.",
          status: "implemented",
          verification: "verified-from-source",
          files: [".vibekb/work/current.md", "guide/templates/current-work.php"],
        },
        {
          id: "hand-off-to-next-agent",
          title: "Hand off to the next agent",
          summary:
            ".vibekb/work/handoff.md records state, completed work, verification, unresolved work, warnings, and the exact next action a fresh agent reads to continue.",
          status: "implemented",
          verification: "verified-from-source",
          files: [".vibekb/work/handoff.md", "guide/templates/handoff.php"],
        },
      ],
    },
    {
      id: "developer-cli",
      title: "The developer CLI",
      blurb:
        "A single vibekb binary that fronts the toolchain: native diagnostics that need no runtime, and honest delegation to the canonical PHP core for every command that touches the model.",
      capabilities: [
        {
          id: "run-the-developer-cli",
          title: "Run VibeKB from one developer CLI",
          summary:
            "A single Go binary installs VibeKB natively (embedded payload, no PHP) and delegates every model-semantic command to the canonical PHP tooling: one model loader.",
          status: "implemented",
          verification: "verified-from-source",
          files: ["cmd/vibekb/main.go", "internal/cli/cli.go", "internal/phpcore/phpcore.go"],
        },
      ],
    },
    {
      id: "dynamic-guide",
      title: "The dynamic guide (Mode A)",
      blurb:
        "The live PHP app that renders the model in the browser: routing, provenance, the interactive map, and search. No build step, no rewrite rules.",
      capabilities: [
        {
          id: "render-guide",
          title: "Render the dynamic guide",
          summary:
            "A single PHP front controller routes by ?view=, loads the model, and renders one shared template set live on every request.",
          status: "implemented",
          verification: "verified-from-source",
          files: ["guide/index.php", "guide/templates/layout.php", "guide/lib/nav.php"],
        },
        {
          id: "render-functionality-map",
          title: "Render the interactive functionality map",
          summary:
            "The overview's first screen is an interactive map (areas expand to capabilities and open into the docs) built from the existing model, degrading to an accessible list without JavaScript.",
          status: "implemented",
          verification: "verified-from-source",
          files: ["guide/lib/map.php", "guide/templates/partials/functionality-map.php", "guide/assets/js/guide.js"],
        },
        {
          id: "show-provenance",
          title: "Show provenance and freshness",
          summary:
            "Every rendering carries an objective provenance panel: source commit, analysis date, verification scope, and an explicit 'does not auto-update.'",
          status: "implemented",
          verification: "verified-from-source",
          files: ["guide/lib/Provenance.php", "guide/templates/overview.php", "guide/templates/layout.php"],
        },
        {
          id: "search-the-model",
          title: "Search the model",
          summary:
            "One shared index covers functionality, memory, files, and diagrams; client-side search needs no server, database, or CDN.",
          status: "implemented",
          verification: "verified-from-source",
          files: ["guide/lib/search.php", "guide/templates/search.php", "guide/assets/js/guide.js"],
        },
      ],
    },
    {
      id: "static-publishing",
      title: "The static snapshot (Mode B)",
      blurb:
        "Rendering the same model through the same templates into a self-contained /docs site for GitHub Pages or any static host.",
      capabilities: [
        {
          id: "generate-static-snapshot",
          title: "Generate the static snapshot",
          summary:
            "php tools/generate-static.php renders the same templates into a self-contained static site with subpath-safe links, refusing to build on validation errors, stamped as generated output.",
          status: "implemented",
          verification: "verified-from-source",
          files: ["tools/generate-static.php", "guide/lib/UrlStrategy.php", "guide/lib/nav.php"],
        },
      ],
    },
    {
      id: "diagrams",
      title: "Explainable diagrams",
      blurb:
        "Source-grounded diagrams whose repository-owned topology gives every node a purpose, every edge a mechanism, and every file a reason.",
      capabilities: [
        {
          id: "render-explainable-diagrams",
          title: "Render explainable diagrams",
          summary:
            "A diagram can carry a topology (nodes with purposes, edges with controlled mechanisms, files with reasons) rendered as semantic explanations that work without JavaScript.",
          status: "implemented",
          verification: "verified-from-source",
          files: ["guide/lib/Content.php", "guide/templates/diagrams.php", "guide/templates/partials/diagram-explain.php"],
        },
        {
          id: "validate-diagram-topology",
          title: "Validate diagram topology",
          summary:
            "The loader and tools/test-topology.php enforce the explainability contract: resolvable edges, controlled mechanisms, honest verification, files with reasons.",
          status: "implemented",
          verification: "verified-by-test",
          files: ["guide/lib/Content.php", "tools/test-topology.php"],
        },
      ],
    },
    {
      id: "integration",
      title: "Adopting VibeKB elsewhere",
      blurb:
        "Installing a VibeKB model inside another repository, safely and reversibly.",
      capabilities: [
        {
          id: "install-into-a-repository",
          title: "Install VibeKB into a repository",
          summary:
            "A native, repository-safe installer. vibekb install consolidates everything VibeKB owns under .vibekb/ and touches shared files only through namespaced adapters: nothing at the repo root by default.",
          status: "implemented",
          verification: "verified-from-source",
          files: ["cmd/vibekb/main.go", "internal/installer/installer.go", "template/manifest.json"],
        },
        {
          id: "bootstrap-workspace",
          title: "Bootstrap the VibeKB workspace",
          summary:
            "A deterministic php tools/vibekb.php bootstrap creates or repairs a .vibekb/ workspace without inspecting source or inventing functionality. 'git init' for VibeKB.",
          status: "implemented",
          verification: "verified-from-source",
          files: ["tools/lib/Starter.php", "tools/vibekb.php", "template/starter/starter.json"],
        },
        {
          id: "migrate-legacy-install",
          title: "Migrate a legacy root-level install",
          summary:
            "vibekb migrate consolidates a pre-2.0 root-level install under .vibekb/, converting only files it can positively identify as unmodified VibeKB content by hash.",
          status: "implemented",
          verification: "verified-from-source",
          files: ["internal/installer/migrate.go", "internal/installer/block.go"],
        },
        {
          id: "uninstall-from-a-repository",
          title: "Uninstall VibeKB from a repository",
          summary:
            "vibekb uninstall removes VibeKB, ownership-aware: it deletes VibeKB-owned files and strips only its managed block from shared files, preserving everything else.",
          status: "implemented",
          verification: "verified-from-source",
          files: ["internal/installer/uninstall.go", "internal/installer/block.go"],
        },
        {
          id: "initialize-in-a-repository",
          title: "Initialize VibeKB in a repository",
          summary:
            "A documented, project-agnostic process for an agent to build a fresh, honest .vibekb/ model of another application read-only, then render it.",
          status: "partial",
          verification: "reported-by-developer",
          files: ["INITIALIZE.md", "prompts/INTEGRATE_VIBEKB.md"],
        },
      ],
    },
    {
      id: "deployment",
      title: "Deployment & portability",
      blurb:
        "How the guide deploys to shared hosting and stays portable with no build step.",
      capabilities: [
        {
          id: "deploy-and-stay-portable",
          title: "Deploy and stay portable",
          summary:
            "The guide deploys to plain PHP 8.2 shared hosting via .cpanel.yml: no build step, no database, no rewrite rules. Query-string routing and relative links run it at a web root or in a subfolder.",
          status: "implemented",
          verification: "verified-from-source",
          files: [".cpanel.yml", "DEPLOYMENT.md", "guide/lib/UrlStrategy.php"],
        },
      ],
    },
  ],

  // Area-to-area relationships, derived from the cross-area `depends_on` links
  // in the real functionality records. These are the real dependencies, not a
  // pleasing arrangement.
  edges: [
    { from: "agent-workflow", to: "living-model" },
    { from: "agent-workflow", to: "static-publishing" },
    { from: "dynamic-guide", to: "living-model" },
    { from: "dynamic-guide", to: "diagrams" },
    { from: "static-publishing", to: "dynamic-guide" },
    { from: "static-publishing", to: "living-model" },
    { from: "diagrams", to: "living-model" },
    { from: "integration", to: "living-model" },
    { from: "integration", to: "developer-cli" },
    { from: "integration", to: "static-publishing" },
    { from: "developer-cli", to: "integration" },
    { from: "developer-cli", to: "living-model" },
    { from: "developer-cli", to: "agent-workflow" },
    { from: "deployment", to: "dynamic-guide" },
    { from: "deployment", to: "static-publishing" },
  ],
};
