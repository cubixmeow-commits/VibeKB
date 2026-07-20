# VibeKB — cPanel Deployment

## Deployment path

```text
/home/iainmcok/public_html/vibekb/
```

cPanel account: `iainmcok`

## Application snapshot (current repository)

| Item | Status |
|------|--------|
| Application type | Documentation / static website project (early stage) |
| Public entry point | Not yet present — add `index.html` (or equivalent) at the repo root when the site is ready |
| Runtime source | None yet beyond repository documentation |
| Production build | None — no `package.json`, bundler config, or build output directory |
| Persistent server data | None committed; common production data paths are protected in `.cpanel.yml` |

As application code is added, update `.cpanel.yml` in the same change (see `AGENTS.md`).

## How deployment works

1. The repository is managed in cPanel **Git™ Version Control**.
2. A checked-in `.cpanel.yml` in the repository root defines deployment tasks.
3. On deploy (automatic after push to the cPanel-managed repo, or manual **Deploy HEAD Commit**), cPanel runs those tasks.
4. The tasks create the destination directory if needed, then `rsync` the repository working tree into the deployment path.

`rsync` uses `--delete` so files removed from the repository are also removed from the web root — except paths listed in `--exclude`, which are not deleted on the destination.

## What gets copied

Everything in the repository checkout **except** the excluded paths below. Today that means there is effectively no public site content yet (documentation and tooling files are excluded). Once you add deployable assets (for example `index.html`, CSS, JS, images), those files will be synced to:

```text
/home/iainmcok/public_html/vibekb/
```

## What is excluded

### Development and VCS

- `.git/`, `.github/`, `.cursor/`, `.vscode/`, `.idea/`
- `.cpanel.yml`, `.gitignore`, `.gitattributes`, `.editorconfig`
- `tests/`, `test/`, `__tests__/`, `docs/`, `coverage/`
- `logs/`, `tmp/`, `temp/`, `cache/`
- `*.log`, `.DS_Store`, `Thumbs.db`

### Secrets and local config

- `.env`, `.env.*`, `*.env`
- `config.local.php`, `local.config.php`

### Project documentation and tooling (not for the web root)

- `README.md`, `DEPLOYMENT.md`, `AGENTS.md`
- `LICENSE`, `LICENSE.*`
- Node/PHP tooling manifests and lockfiles (`package.json`, lockfiles, `composer.json`, etc.)
- Bundler/linter/test configs (`vite.config.*`, `eslint.config.*`, `phpunit.xml*`, Dockerfiles, etc.)

### Host / SSL paths

- `.well-known/` (kept on the server; not overwritten or deleted by deploy)

## Persistent directories protected

These paths are excluded from sync **and** from `--delete`, so production data survives deploys even if they are not in Git:

| Path / pattern | Purpose |
|----------------|---------|
| `uploads/` | Uploaded files |
| `storage/` | App storage |
| `data/` | Server-side data |
| `media/` | Media / user content |
| `sessions/` | Session files |
| `cache/` | Persistent cache |
| `var/`, `runtime/`, `private/` | Runtime / private server data |
| `*.sqlite`, `*.sqlite3`, `*.db` | SQLite (or similar) databases |
| `config.local.php`, `local.config.php` | Server-only configuration |
| `.env`, `.env.*` | Environment secrets |

Create these on the server as needed; do not rely on deploy to create writable data directories from Git.

## How to deploy from cPanel

### One-time setup

1. In cPanel, open **Git™ Version Control**.
2. Create or clone this repository on the account (keep the Git checkout **outside** `public_html` if possible).
3. Confirm `.cpanel.yml` is present at the repository root on the deployed branch.
4. Ensure the working tree is clean (no uncommitted local changes in the cPanel checkout).

### Push (automatic) deployment

1. Push to the remote that cPanel tracks (or push directly to the cPanel-managed repository).
2. If the cPanel repo receives the commit and `.cpanel.yml` is valid, the post-receive hook runs the deployment tasks.

### Pull (manual) deployment

1. In **Git™ Version Control**, open the repository → **Manage**.
2. Use **Update from Remote** if you pull from GitHub/GitLab/etc.
3. Click **Deploy HEAD Commit** to run `.cpanel.yml`.

### Verify

1. Confirm files under `/home/iainmcok/public_html/vibekb/` via File Manager or SSH.
2. Visit the site URL that maps to that directory.
3. If something failed, check cPanel Git deployment logs (typically under `~/.cpanel/logs/` with a `vc_*_git_deploy` name).

## Build process notes

- This repository currently has **no frontend build**.
- Do **not** assume Node.js is available on shared hosting.
- When a build is introduced later:
  - Prefer committing compiled production assets (for example `dist/` or `public/`), **or**
  - Build in CI and deploy artifacts — not by running `npm install` / `npm run build` inside `.cpanel.yml` on the cPanel host unless the host is known to support it.
  - Update `.cpanel.yml` exclusions so source-only trees are skipped and the build output is deployed.

## PHP notes

No PHP application is present yet. When PHP is added:

- Preserve the runtime layout expected by the app.
- Keep `.env` and local config files out of deploy (already excluded).
- Protect writable directories with `--exclude` (already covered for common names).
- Use only safe ownership/permissions if needed; **never** `chmod 777`.

## Common troubleshooting

| Symptom | Likely cause | What to try |
|---------|--------------|-------------|
| Deploy button disabled / no deploy info | Missing or invalid `.cpanel.yml`, dirty working tree, or no branches | Fix YAML, commit it to the repo root, commit/stash local changes on the server checkout |
| Site empty after deploy | No public entry file in the repo (current state), or entry file excluded | Add `index.html` (or PHP front controller) and ensure it is not excluded |
| Old files remain | Deploy did not run, or path mismatch | Confirm `DEPLOYPATH` and that Deploy HEAD Commit ran successfully |
| Uploads / DB wiped | Persistent path not excluded | Add the path to `--exclude` in `.cpanel.yml` and redeploy |
| Secrets on the server from Git | Secret committed in repo | Rotate the secret; keep `.env*` excluded; remove secrets from Git history if needed |
| YAML / task errors | Invalid YAML or bad shell quoting | Validate YAML locally; keep each task a valid bash command |
| SSL / ACME files missing | `.well-known` overwritten | Keep `.well-known/` excluded (already configured) |

## Maintenance

Whenever the repository structure, build output, runtime folders, or hosting requirements change, review and update `.cpanel.yml` in the same task. See **Deployment Maintenance** in `AGENTS.md`.
