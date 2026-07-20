# VibeKB Deployment Guide

This project deploys to a cPanel shared hosting account with Git Version Control and a `.cpanel.yml` deployment file.

## Deployment path

| Item | Value |
|------|--------|
| cPanel username | `iainmcok` |
| Deployment destination | `/home/iainmcok/public_html/vibekb/` |
| Public URL path | `/vibekb/` (under the account’s primary domain, unless a subdomain/addon domain is pointed here) |

## Application profile (current repository)

As of the latest inspection, this repository contains:

- `README.md` — project description
- Deployment/maintenance docs (`DEPLOYMENT.md`, `AGENTS.md`)
- `.cpanel.yml` — cPanel deploy tasks

There is **no application runtime yet** (no `index.html`, PHP app, Node server, or production build output).

| Detected item | Status |
|---------------|--------|
| Application type | Placeholder / documentation-only repository (intended website product) |
| Public entry point | Not present yet; add a web root entry such as `index.html` or `index.php` when the site is implemented |
| Production build output | None detected |
| Persistent server data | None in the repo; common production paths are protected by rsync excludes |

When application files are added, update `.cpanel.yml` in the same change (see [AGENTS.md](./AGENTS.md)).

## How deployment works

1. The repository is managed in cPanel **Git Version Control**.
2. On deploy (automatic after push, or manual via the cPanel UI), cPanel runs the shell tasks in `.cpanel.yml`.
3. Those tasks:
   - Set `DEPLOYPATH` to `/home/iainmcok/public_html/vibekb/`
   - Ensure the destination directory exists
   - Sync the checked-out repository into that path with `/bin/rsync -av --delete`
4. Development-only files and secrets are excluded from the sync.
5. Persistent production directories/files are also excluded so `--delete` cannot remove them on the server.

cPanel runs the `.cpanel.yml` tasks against the **cPanel-managed clone** of the repository, not against your local workstation.

## What gets copied

Everything in the repository checkout **except** excluded paths is synced to:

`/home/iainmcok/public_html/vibekb/`

Today that effectively means deployable project files such as `README.md`. Once the site is built out, HTML/CSS/JS/PHP (or built static assets committed to the repo) will be deployed the same way.

**Important:** The production server is not assumed to have Node.js. Do not rely on `npm install` / `npm run build` inside `.cpanel.yml` on this shared host. If a frontend build is required later, build in CI or locally and commit/publish the compiled assets (or adjust deployment after confirming available tooling on the host).

## What is excluded

Development, tooling, secrets, and meta files are excluded, including:

| Category | Examples |
|----------|----------|
| VCS / IDE | `.git/`, `.github/`, `.cursor/`, `.vscode/`, `.idea/` |
| Deploy config | `.cpanel.yml` |
| Secrets / env | `.env`, `.env.*` |
| Dependencies | `node_modules/`, `vendor/` |
| Tests / docs / coverage | `tests/`, `test/`, `__tests__/`, `docs/`, `coverage/` |
| Logs / temp | `logs/`, `tmp/`, `temp/`, `*.log` |
| OS junk | `.DS_Store`, `Thumbs.db` |
| Tooling manifests | `package.json`, lockfiles, `composer.json`, `tsconfig*.json`, bundler configs, Docker/Make files |
| Agent / deploy docs | `AGENTS.md`, `DEPLOYMENT.md`, `CONTRIBUTING.md` |
| Host metadata | `.well-known/` |

See `.cpanel.yml` for the authoritative exclude list.

## Persistent directories protected

These paths are excluded from rsync so `--delete` will **not** remove them if they exist only on the server:

| Path / pattern | Purpose |
|----------------|---------|
| `uploads/`, `upload/` | Uploaded files |
| `media/`, `files/` | User/media content |
| `storage/` | App storage |
| `sessions/`, `session/` | Session data |
| `cache/` | Persistent cache (if used in production) |
| `data/`, `database/`, `databases/`, `sqlite/` | Server-side data directories |
| `*.sqlite`, `*.sqlite3`, `*.db` | SQLite / local DB files |
| `config.local.php`, `config.production.php`, `local.config.php`, `wp-config.php` | Server-side configuration |
| `.env`, `.env.*` | Environment secrets (never deployed from git) |

If the application later introduces other persistent paths (for example `public/uploads/` or `var/`), add matching `--exclude` entries to `.cpanel.yml` before the next deploy.

## How to deploy from cPanel

### One-time setup

1. In cPanel, open **Git Version Control**.
2. Create or clone the VibeKB repository on the server (clone path is separate from the public deploy path).
3. Confirm `.cpanel.yml` is present at the repository root on the branch you deploy.
4. Ensure the deploy destination `/home/iainmcok/public_html/vibekb/` is the intended public directory.

### Deploy updates

**Push (automatic) deployment**

1. Push commits to the branch tracked by the cPanel repository.
2. If push deployment is enabled, cPanel runs `.cpanel.yml` after the update.

**Pull / manual deployment**

1. In **Git Version Control**, open the repository.
2. Update/pull the latest commits if needed.
3. Click **Deploy HEAD Commit** (wording may vary by cPanel version).

### After the first real site files land

1. Verify `https://<your-domain>/vibekb/` (or the mapped domain) serves the expected entry file.
2. Confirm uploads/data directories still exist after deploy.
3. Confirm `.env` and other secrets on the server were not overwritten or removed.

## Common deployment troubleshooting

| Symptom | Likely cause | What to check |
|---------|----------------|---------------|
| Deploy button missing / errors about `.cpanel.yml` | Missing or invalid YAML | File must be at repo root, start with `---`, and contain valid `deployment.tasks` |
| Site empty or old files remain | Deploy did not run, or wrong branch | Deploy HEAD; confirm cPanel is on the intended branch |
| Files deleted unexpectedly | Missing rsync exclude for persistent data | Add `--exclude` for that path; restore from backup if needed |
| Secrets appeared in public_html | Env/config committed to git | Remove from git history if needed; keep excludes; store secrets only on the server |
| Build assets missing | Build not run before deploy / Node unavailable on host | Build externally; deploy compiled output; update excludes if the build directory name changes |
| Permission errors writing uploads | Directory missing or not writable by the web user | Create the directory on the server; use safe modes (for example `755` dirs / `644` files). Never use `chmod 777` |
| `.well-known` challenges broken | Accidental sync/delete | `.well-known/` is excluded; recreate on server if required for SSL/domain validation |
| `rsync: command not found` | Unusual host layout | Confirm `/bin/rsync` exists in the cPanel account shell; adjust the binary path only if the host documents a different location |

## Maintenance

Whenever the repository structure changes, update `.cpanel.yml` in the same task. The deployment configuration is part of the application and must stay accurate. See **Deployment Maintenance** in [AGENTS.md](./AGENTS.md).
