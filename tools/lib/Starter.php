<?php

declare(strict_types=1);

/**
 * VibeKB starter-workspace definition — the single source of truth for what a
 * fresh, empty `.vibekb/` model looks like.
 *
 * Both the installer (`install.php`) and the `vibekb bootstrap` command build
 * and repair a workspace from the definitions here, so there is exactly one
 * place that decides the starter structure. Nothing in this file inspects the
 * target application, invents functionality, or writes documentation about the
 * software — it only lays down empty, valid scaffolding for an agent to fill in.
 *
 * Honesty boundary: the scaffolding it writes describes VibeKB's *content
 * model*, never the target's *software*. Every starter record is explicitly a
 * placeholder that tells the agent what to write; none claims the target does
 * anything.
 *
 * PHP 8.2+, no Composer, no framework, no network. Windows/macOS/Linux safe.
 */

/**
 * Directories that must exist inside a `.vibekb/` workspace, relative to it.
 * Some hold starter files; some (records/, assets/, sessions/, memory types)
 * are intentionally empty until an agent adds content, so bootstrap can still
 * confirm and recreate them.
 *
 * @return list<string>
 */
function vibekb_starter_dirs(): array
{
    return [
        'project',
        'functionality',
        'functionality/records',
        'system',
        'files',
        'diagrams',
        'diagrams/records',
        'diagrams/assets',
        'diagrams/topology',
        'memory',
        'memory/decisions',
        'memory/constraints',
        'memory/assumptions',
        'memory/warnings',
        'memory/discoveries',
        'memory/changes',
        'work',
        'work/sessions',
    ];
}

/**
 * The starter files that must exist inside a `.vibekb/` workspace, as a map of
 * path (relative to the `.vibekb/` root) to file contents. These are the
 * scaffolding an empty-but-valid model needs so it loads cleanly and passes
 * `php tools/vibekb.php check`, while making it obvious that the model has not
 * been built yet.
 *
 * @param array<string,string> $ctx Optional context: `date` (YYYY-MM-DD),
 *                                   `project_name`. Only used to date-stamp and
 *                                   label starter records — never to describe
 *                                   the software.
 * @return array<string,string>
 */
function vibekb_starter_files(array $ctx = []): array
{
    $date = $ctx['date'] ?? date('Y-m-d');
    $name = trim($ctx['project_name'] ?? '') !== '' ? $ctx['project_name'] : 'This project';
    $nameJson = json_encode($name, JSON_UNESCAPED_SLASHES);

    $files = [];

    // ---- manifest -----------------------------------------------------------
    // Provenance is intentionally blank: the installer does NOT analyse the
    // repository, so it cannot honestly record a commit or verification scope.
    // The agent fills this in after building the model (INITIALIZE.md step 7).
    $files['manifest.json'] = <<<JSON
{
  "vibekb_version": "1.0",
  "content_model": "functionality-first",
  "updated": "{$date}",
  "self_hosted": false,
  "example_project": {
    "name": {$nameJson},
    "is_sample": false,
    "note": "Fresh VibeKB workspace created by the installer. The model has not been built yet — no functionality has been analysed. An AI coding agent builds it by following prompts/INTEGRATE_VIBEKB.md and INITIALIZE.md."
  },
  "provenance": {
    "name": {$nameJson},
    "source_repository": "",
    "source_subpath": "",
    "source_branch": "",
    "source_commit": "",
    "analyzed": "",
    "verification_scope": "Not yet analysed. This is an empty starter workspace. Fill this in after building the model against a specific commit.",
    "last_verified": "",
    "updates_automatically": false,
    "model_maintenance": "agent-maintained",
    "freshness_note": "VibeKB does not update this model on its own. An AI agent builds and maintains it by following the workflow in CLAUDE.md and INITIALIZE.md. Until the model is built, this workspace is empty scaffolding only."
  }
}
JSON;

    // ---- functionality index ------------------------------------------------
    // Empty groups + order: a valid model with zero functionality. The agent
    // defines real functional areas while building the model.
    $files['functionality/index.json'] = <<<'JSON'
{
  "_comment": "Define functional areas as { id, title, description } in groups[], and list functionality record ids in display order in order[]. Do NOT invent functionality — trace it from source. See SCHEMA.md and INITIALIZE.md.",
  "groups": [],
  "order": []
}
JSON;

    // ---- important files ----------------------------------------------------
    $files['files/important-files.json'] = <<<'JSON'
{
  "_comment": "Curate the files that matter, each with path, purpose, functionality[], runs_when, depended_on_by[], depends_on[], safety, test_after_change, provenance. See SCHEMA.md. Empty until the agent builds the model.",
  "files": []
}
JSON;

    // ---- diagrams index -----------------------------------------------------
    $files['diagrams/index.json'] = <<<'JSON'
{
  "_comment": "Define diagram groups as { id, title, description } in groups[], and list diagram record ids in display order in order[]. Only add source-grounded, explainable diagrams. See SCHEMA.md and INITIALIZE.md.",
  "groups": [],
  "order": []
}
JSON;

    // ---- project docs (placeholders — describe the CONTENT MODEL, not the app)
    $files['project/identity.md'] = vibekb_starter_project_doc(
        'identity',
        'What this software is',
        'STARTER PLACEHOLDER — replace with a one-sentence statement of what this software is (its purpose, users, and the outcome it produces).',
        $date,
        <<<'MD'
> **This is a starter placeholder.** The installer does not analyse your code.
> An AI agent fills this in while building the model.

Describe, in plain language, **what this software is**: its purpose, who uses it,
what outcome it produces, and its scope and non-goals. Trace this from the actual
source and the repository's own docs — do not assume the README is accurate.

See `PRODUCT.md` for how VibeKB thinks about identity, and `INITIALIZE.md` for
the full build workflow.
MD
    );

    $files['project/intent.md'] = vibekb_starter_project_doc(
        'intent',
        'Why this software exists',
        'STARTER PLACEHOLDER — replace with why this software exists and what it must not become.',
        $date,
        <<<'MD'
> **This is a starter placeholder.** Replace it while building the model.

Explain **why this software exists** — the problem it solves — and, importantly,
**what it must not become** (its non-goals). Keep this grounded in what the code
and the project's own intent actually say.
MD
    );

    $files['project/current-state.md'] = vibekb_starter_project_doc(
        'current-state',
        'What this software does right now',
        'STARTER PLACEHOLDER — replace with an honest summary of what is implemented, partial, planned, and unverified right now.',
        $date,
        <<<'MD'
> **This is a starter placeholder.** Replace it while building the model.

Summarise **what the software actually does right now**, separating what is
**implemented**, **partial**, **planned**, **broken**, and **unknown**. Be honest
about verification — never present intended or planned behaviour as implemented.
MD
    );

    $files['project/constraints.md'] = vibekb_starter_project_doc(
        'constraints',
        'Boundaries this software runs inside',
        'STARTER PLACEHOLDER — replace with the real runtime, platform, and design boundaries this software must respect.',
        $date,
        <<<'MD'
> **This is a starter placeholder.** Replace it while building the model.

List the **boundaries the software runs inside** — runtime/platform limits,
hard design constraints, and anything a change must not break. Ground each one in
evidence from the repository.
MD
    );

    // ---- system docs (placeholders) ----------------------------------------
    $system = [
        'mental-model'  => ['Mental model', 'the one-paragraph way to think about how this software is structured'],
        'components'    => ['Components', 'the major parts of the system and what each is responsible for'],
        'request-flow'  => ['Request flow', 'how a request (or invocation) travels through the system to a result'],
        'data-flow'     => ['Data flow', 'how data moves through the system, and in which direction'],
        'storage'       => ['Storage', 'where and how the software stores data (databases, files, caches, sessions)'],
        'deployment'    => ['Deployment', 'how the software is built, deployed, and run in production'],
    ];
    foreach ($system as $slug => [$title, $desc]) {
        $files['system/' . $slug . '.md'] = vibekb_starter_system_doc($slug, $title, $desc, $date);
    }

    // ---- work records -------------------------------------------------------
    $files['work/current.md'] = <<<MD
---
id: current-work
type: work
title: Build the first VibeKB model for this repository
objective: The workspace is scaffolded but empty. Build the first living software model for this repository by following prompts/INTEGRATE_VIBEKB.md and INITIALIZE.md.
summary: Fresh install — no model built yet. See handoff.md for the next action.
requested_by: installer
status: planned
verification_state: not-applicable
updated: {$date}
affected_functionality: []
expected_files: []
data_impact: None — VibeKB reads the software; it does not change it.
risks: []
---

## Status

This is a **fresh VibeKB workspace** created by the installer. The scaffolding
exists and is valid, but **no functionality has been analysed yet** — the model
is empty on purpose.

## Next

Hand this to an AI coding agent (Claude Code, Cursor, Codex, …) and have it build
the first model by following **`prompts/INTEGRATE_VIBEKB.md`** and the 17-step
workflow in **`INITIALIZE.md`**. The installer prepares the workspace; the agent
interprets the software.
MD;

    $files['work/handoff.md'] = <<<MD
---
id: handoff
type: handoff
title: Current handoff
summary: Fresh VibeKB workspace — scaffolded and valid, but the model has not been built yet. Next: an AI agent builds the first model from source following prompts/INTEGRATE_VIBEKB.md.
updated: {$date}
verification_state: not-applicable
---

## Current state

VibeKB was just installed into this repository. The `.vibekb/` workspace has the
full directory structure and valid starter files, and it passes
`php tools/vibekb.php check`. **It contains no functionality records yet** — the
model is intentionally empty until an agent builds it.

## Completed

- Runtime installed (`guide/`, `tools/`, `prompts/`, `.cursor/`, VibeKB docs).
- Empty, valid `.vibekb/` model scaffolded (project, functionality, system,
  files, diagrams, memory, work).

## Not done yet

- **The model itself.** No software has been analysed. Provenance in
  `manifest.json` is intentionally blank until the model is built against a
  specific commit.

## Next recommended action

Open the repository in your coding agent and run the integration prompt:
**build the first VibeKB model for this repository using
`prompts/INTEGRATE_VIBEKB.md`** (it drives the `INITIALIZE.md` workflow). Then
run `php tools/vibekb.php check` and, if you want the static snapshot,
`php tools/vibekb.php generate`.
MD;

    // ---- access control -----------------------------------------------------
    $files['.htaccess'] = <<<'HT'
# Deny direct web access to knowledge files (Apache).
# The PHP engine reads these from the filesystem.
<IfModule mod_authz_core.c>
  Require all denied
</IfModule>
<IfModule !mod_authz_core.c>
  Deny from all
</IfModule>
HT;

    return $files;
}

/** Build a starter project document body with front matter. */
function vibekb_starter_project_doc(string $id, string $title, string $summary, string $date, string $body): string
{
    return "---\n"
        . "id: {$id}\n"
        . "type: project\n"
        . "title: {$title}\n"
        . "summary: {$summary}\n"
        . "updated: {$date}\n"
        . "---\n\n"
        . rtrim($body) . "\n";
}

/** Build a starter system document body with front matter. */
function vibekb_starter_system_doc(string $id, string $title, string $describe, string $date): string
{
    $summary = 'STARTER PLACEHOLDER — replace with ' . $describe . '.';
    $body = <<<MD
> **This is a starter placeholder.** The installer does not analyse your code.
> An AI agent fills this in while building the model.

Explain **{$describe}**. Ground the explanation in the actual source; label
anything you infer rather than verify. See `INITIALIZE.md` and `SCHEMA.md`.
MD;
    return "---\n"
        . "id: {$id}\n"
        . "type: system\n"
        . "title: {$title}\n"
        . "summary: {$summary}\n"
        . "updated: {$date}\n"
        . "---\n\n"
        . rtrim($body) . "\n";
}

/**
 * Inspect a `.vibekb/` workspace and report which starter directories and files
 * are missing, without changing anything. Used by `bootstrap` (and the
 * installer's verification) to describe the state of an installation.
 *
 * @return array{ok: bool, missing_dirs: list<string>, missing_files: list<string>, present: bool}
 */
function vibekb_verify_workspace(string $vibekbRoot, array $ctx = []): array
{
    $vibekbRoot = rtrim($vibekbRoot, '/\\');
    $present = is_dir($vibekbRoot);

    $missingDirs = [];
    foreach (vibekb_starter_dirs() as $rel) {
        if (!is_dir($vibekbRoot . '/' . $rel)) {
            $missingDirs[] = $rel;
        }
    }

    $missingFiles = [];
    foreach (array_keys(vibekb_starter_files($ctx)) as $rel) {
        if (!is_file($vibekbRoot . '/' . $rel)) {
            $missingFiles[] = $rel;
        }
    }

    return [
        'ok' => $present && $missingDirs === [] && $missingFiles === [],
        'missing_dirs' => $missingDirs,
        'missing_files' => $missingFiles,
        'present' => $present,
    ];
}

/**
 * Create or repair a `.vibekb/` workspace: create every required directory and
 * write every missing starter file. Existing files are left untouched (never
 * overwritten) unless `$force` is true, so it is always safe to run against a
 * partial or damaged workspace — the "git init for VibeKB" guarantee.
 *
 * When `$dryRun` is true nothing is written; the returned report describes what
 * would happen.
 *
 * @param array<string,string> $ctx Context passed to vibekb_starter_files().
 * @return array{created_dirs: list<string>, created_files: list<string>, kept_files: list<string>, overwritten_files: list<string>, errors: list<string>}
 */
function vibekb_scaffold_workspace(string $vibekbRoot, array $ctx = [], bool $dryRun = false, bool $force = false): array
{
    $vibekbRoot = rtrim($vibekbRoot, '/\\');
    $report = [
        'created_dirs' => [],
        'created_files' => [],
        'kept_files' => [],
        'overwritten_files' => [],
        'errors' => [],
    ];

    // The workspace root itself.
    if (!is_dir($vibekbRoot)) {
        $report['created_dirs'][] = '.';
        if (!$dryRun && !@mkdir($vibekbRoot, 0755, true) && !is_dir($vibekbRoot)) {
            $report['errors'][] = "Could not create workspace directory: {$vibekbRoot}";
            return $report;
        }
    }

    foreach (vibekb_starter_dirs() as $rel) {
        $path = $vibekbRoot . '/' . $rel;
        if (!is_dir($path)) {
            $report['created_dirs'][] = $rel;
            if (!$dryRun && !@mkdir($path, 0755, true) && !is_dir($path)) {
                $report['errors'][] = "Could not create directory: {$rel}";
            }
        }
    }

    foreach (vibekb_starter_files($ctx) as $rel => $contents) {
        $path = $vibekbRoot . '/' . $rel;
        $exists = is_file($path);
        if ($exists && !$force) {
            $report['kept_files'][] = $rel;
            continue;
        }
        if ($exists && $force) {
            $report['overwritten_files'][] = $rel;
        } else {
            $report['created_files'][] = $rel;
        }
        if ($dryRun) {
            continue;
        }
        $dir = dirname($path);
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        if (@file_put_contents($path, $contents) === false) {
            $report['errors'][] = "Could not write file: {$rel}";
        }
    }

    return $report;
}
