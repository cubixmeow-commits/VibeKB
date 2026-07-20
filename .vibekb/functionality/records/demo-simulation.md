---
id: demo-simulation
type: functionality
title: Demo Mode & simulation
area: administration
summary: Two related things — the Runner's per-recipe "paste example" for a no-AI walkthrough, and a portfolio simulation that populates hundreds of labelled demo creators and a public activity dashboard.
status: partial
verification: inferred-from-source
user_facing: true
trigger: A user clicks "Paste example response"; or an operator runs the simulation scripts.
updated: 2026-07-16
tags: [demo, simulation, portfolio]
files: [app/Controllers/RunnerController.php, app/Services/Simulation.php, app/Services/SimulationKitchen.php, app/Services/SiteStats.php, scripts/simulate-users.php, scripts/simulate-day.php, database/simulation/personas.json]
reads: [cookbooks, recipes, users, projects, artifacts, exports, simulation_runs]
writes: [users, projects, pantry_values, artifacts, artifact_versions, exports, simulation_runs]
config: []
depends_on: [paste-response]
related_memory: [discovery:demo-mode-labeling, decision:never-calls-ai]
---

## In one sentence

Try the whole loop with no AI by pasting seeded examples, and — for the
portfolio — populate hundreds of clearly-labelled simulated creators with daily
activity.

## Current behavior

**Demo Mode (verified):** on each Recipe, `pasteExample()` stores the recipe's
seeded `example_response` as a version marked sample data, so the full
create-to-export loop works without any AI. Sample data is always labelled in
the UI, manifest, and kit.

**Simulation (inferred):** `scripts/simulate-users.php` and `simulate-day.php`
(run daily via cron) create `simulation = 1` users and daily Pacific-time
activity; `SiteStats` reads only these simulation rows to render public metrics
and a GitHub-style activity heatmap; `simulation_runs` records each day. Demo
login credentials are documented in `README.md`/`docs/SIMULATION.md`.

## Implementation map

- `app/Controllers/RunnerController.php` — `pasteExample()` (verified).
- `app/Services/Simulation.php`, `SimulationKitchen.php` — the simulator (read at
  the boundary, not fully traced).
- `app/Services/SiteStats.php` — public/admin metrics from simulation rows
  (verified).
- `scripts/simulate-*.php`, `database/simulation/personas.json`.

## Data used

- **Reads/Writes:** simulation users and their projects/artifacts/exports; the
  `simulation_runs` day log.

## Current state

- **Status:** partial — the "paste example" Demo Mode is source-verified; the
  bulk simulation scripts are **inferred from source** (README + `SiteStats`
  queries confirm the shape; the `Simulation*` internals were not fully traced).
- **Verification:** inferred-from-source.

## Use caution

`SiteStats` deliberately counts only `simulation = 1` rows, so demo metrics
never mix with real users. Keep that filter when touching those queries.

## Why it works this way

The simulation exists so the portfolio can show a lively product without real
traffic — and because SousMeow never calls an AI, every example is seeded, not
generated (`never-calls-ai`).

## Related functionality

- Paste a response
- View the admin overview
