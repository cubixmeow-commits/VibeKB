# AGENTS.md — Entry point for coding agents

The **canonical, repository-owned** operating rules and maintenance lifecycle for
every coding agent live in **[CLAUDE.md](./CLAUDE.md)**. This file exists so
tools that look for `AGENTS.md` (Codex and others) find the same workflow. It is a
pointer, not a second copy — if the two ever disagree, `CLAUDE.md` wins.

## The 30-second version

1. **Start every session with** `php tools/vibekb.php status` — it tells you the
   provenance, the current work, the handoff's next action, and any drift.
2. **VibeKB is self-hosted.** The active `.vibekb/` describes VibeKB itself. Other
   apps' models live under `examples/` and are **not** the active model.
3. **The product is locked.** VibeKB explains **what the software is currently
   doing**, organized around **functionality**. Do not turn it into a docs
   generator, memory archive, code browser, or activity log. See
   [PRODUCT.md](./PRODUCT.md).
4. **Follow the lifecycle** in [CLAUDE.md](./CLAUDE.md): orient → record work →
   implement → `affected` → verify → update the model → handoff → `check` +
   `generate`. Writing code is not "done."
5. **Be honest.** Distinguish intended / implemented / verified. Never claim
   `verified-*` you did not confirm. Never imply VibeKB auto-updates.
6. **Before finishing:** `php tools/vibekb.php check` clean,
   `php tools/test-topology.php` OK, `/docs` regenerated, handoff accurate.

## Deployment

Whenever repository structure, runtime folders, or deployment requirements
change, update `.cpanel.yml` and `DEPLOYMENT.md` in the same change (see
[DEPLOYMENT.md](./DEPLOYMENT.md)). Keep the app on PHP 8.2 with no build step, no
database, and no external/AI API.
