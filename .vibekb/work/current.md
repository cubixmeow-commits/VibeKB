---
id: current-work
type: work
title: Repository-safety redesign of the installer footprint
objective: Make `vibekb install .` safe in fresh, active, and mature repositories — consolidate everything VibeKB owns under `.vibekb/`, integrate with shared files only via namespaced adapters or a marked managed block, and add migrate/uninstall/doctor + a per-install manifest.
summary: Complete — audit, consolidated payload map, managed-block engine, safe install flags, install manifest, migration, uninstall, doctor footprint, layout-aware PHP, tests, and docs. Verified via go test + end-to-end install/uninstall/migrate and `php tools/vibekb.php check`.
requested_by: User
status: complete
verification_state: verified-from-source
updated: 2026-07-23
affected_functionality: [install-into-a-repository, migrate-legacy-install, uninstall-from-a-repository, run-the-developer-cli, render-guide, detect-drift, generate-static-snapshot, validate-model, validate-diagram-topology, bootstrap-workspace]
expected_files: [template/manifest.json, embed.go, internal/installer/installer.go, internal/installer/plan.go, internal/installer/apply.go, internal/installer/block.go, internal/installer/manifest.go, internal/installer/state.go, internal/installer/scaffold.go, internal/installer/uninstall.go, internal/installer/migrate.go, internal/installer/doctor.go, internal/cli/cli.go, internal/cli/doctor.go, internal/phpcore/phpcore.go, guide/lib/workspace.php, guide/index.php, tools/vibekb.php, tools/generate-static.php, tools/validate.php, tools/test-topology.php, template/integrations/WORKFLOW.md, template/integrations/agents-block.md, template/integrations/cursor.mdc, template/integrations/vibekb.instructions.md, docs/REPOSITORY_FOOTPRINT_AUDIT.md, docs/REPOSITORY_SAFETY.md]
data_impact: Changes the installed footprint in TARGET repositories (everything now under `.vibekb/`; shared files touched only via a managed block). VibeKB's own self-hosted layout is unchanged. Introduces `.vibekb/install.json` (replaces legacy `.vibekb/.installer.json`).
risks:
  - Two supported PHP layouts (self-hosted root vs consolidated `.vibekb/runtime/`) resolved by `guide/lib/workspace.php`; self-hosted behavior kept byte-identical and re-verified with `check`.
  - Migration of legacy installs is content/hash-gated; ambiguous files are preserved, not deleted.
---

## Status

Complete. See handoff for the full summary, verification, and any residual limits.
