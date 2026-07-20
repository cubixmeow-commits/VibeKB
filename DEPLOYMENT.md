# VibeKB Deployment

cPanel Git Version Control deployment for the VibeKB project.

## Deployment path

| Item | Value |
| --- | --- |
| cPanel username | `iainmcok` |
| Destination | `/home/iainmcok/public_html/vibekb/` |
| Config file | `.cpanel.yml` (repository root) |

## Application inspection (current repository)

| Item | Finding |
| --- | --- |
| Application type | Documentation / website project (early stage) |
| Public entry point | Not yet present; repository currently contains `README.md` only |
| Runtime source files | None detected beyond `README.md` |
| Static assets | None detected |
| Production build output | None detected (no `package.json`, no build scripts, no `dist/` / `build/` / `public/`) |
| Persistent server data | None detected in the repository |
| PHP / Node runtime | Not detected |

When application code is added, update `.cpanel.yml` so deployment matches the new structure. See [AGENTS.md](./AGENTS.md).

## How deployment works

1. The repository is managed in cPanel **Git Version Control**.
2. On deploy, cPanel runs the tasks in `.cpanel.yml`.
3. Those tasks:
   - Set `DEPLOYPATH` to `/home/iainmcok/public_html/vibekb/`
   - Create the destination directory if needed
   - Sync the checked-out repository into `$DEPLOYPATH` with `rsync -av --delete`
4. `--delete` removes files from the destination that are no longer in the deploy set.
5. Excluded paths are **not** copied and are **not** deleted from the destination by rsync, which protects secrets, tooling, and persistent production data.

**Important:** This host is shared cPanel hosting. Do not rely on Node.js, Composer, or other build tools being available on the server. If a frontend build is introduced later, compile assets locally or in CI and deploy the compiled output only.

## What gets copied

Everything in the repository checkout **except** the exclusion list in `.cpanel.yml`.

Today that effectively means:

- `README.md` (only tracked application content at inspection time)

As the project grows, typical deployable content will include public HTML/CSS/JS, images, and any PHP or static runtime files that are not excluded.

## What is excluded

### Always excluded (development / tooling / secrets)

- `.git/`, `.github/`, `.cursor/`, `.vscode/`, `.idea/`
- `.cpanel.yml`
- `.env`, `.env.*`
- `node_modules/`, `vendor/`
- `tests/`, `test/`, `__tests__/`, `docs/`, `coverage/`
- `logs/`, `tmp/`, `temp/`, `cache/`
- `*.log`, `.DS_Store`, `Thumbs.db`
- Editor / package / build config files (for example `.gitignore`, `package.json`, lockfiles, `composer.json`, `vite.config.*`, `tsconfig*.json`, `phpunit.xml*`)
- Ops / agent documentation: `AGENTS.md`, `DEPLOYMENT.md`

### Persistent production paths (protected from `--delete`)

These are excluded so rsync never overwrites or removes them on the server, even if they are not present in the repository:

- `uploads/`, `storage/`, `data/`, `database/`, `databases/`
- `sessions/`, `session/`, `media/`, `files/`, `user-content/`, `runtime/`
- `*.sqlite`, `*.sqlite3`, `*.db`
- Local / production-only config: `config.local.php`, `config.production.php`, `local_config.php`, `.htpasswd`
- cPanel / SSL challenge data: `.well-known/`

If the application introduces a new persistent directory (uploads, SQLite DB location, writable cache, sessions, media, etc.), add a matching `--exclude` in `.cpanel.yml` in the same change.

## How to deploy from cPanel

### One-time setup

1. In cPanel, open **Git Version Control**.
2. Clone or create this repository on the server (keep the Git checkout outside the public web root when possible).
3. Confirm `.cpanel.yml` is present in the repository root on the branch you deploy.
4. Ensure the destination directory exists or can be created: `/home/iainmcok/public_html/vibekb/`.

### Manual deploy

1. Open **Git Version Control** → manage this repository.
2. Use **Pull or Deploy** (wording varies by cPanel version).
3. Update from remote if needed, then **Deploy HEAD Commit**.
4. Confirm files appear under `/home/iainmcok/public_html/vibekb/`.

### Push / automatic deploy

If automatic deployment is enabled for the cPanel-managed repository, a push that updates the tracked branch will run the `.cpanel.yml` tasks after the repository updates.

## Common deployment troubleshooting

| Symptom | Likely cause | What to check |
| --- | --- | --- |
| Deploy button missing / no tasks run | `.cpanel.yml` missing or invalid | File must be in repo root; YAML must start with `---` and contain `deployment.tasks` |
| Destination empty or incomplete | Everything excluded, or wrong branch deployed | Review excludes; confirm HEAD contains the intended files |
| Old files remain after deploy | Path not covered by rsync source, or excluded | Confirm task uses `./ "$DEPLOYPATH"` and that the path is not excluded |
| Production uploads / DB wiped | Persistent path not excluded | Add `--exclude` for that path; restore from backup |
| Site shows secrets or `.env` | Secret file was committed and not excluded | Rotate secrets; keep `.env` out of git; confirm `.env` exclude remains |
| Build assets missing | Build expected on server | Build locally/CI; commit or artifact the output directory; deploy compiled files only |
| Permission errors | Destination not writable by cPanel user | Ownership/permissions under `/home/iainmcok/public_html/vibekb/` (never use `chmod 777`) |
| SSL / ACME issues after deploy | `.well-known` overwritten | Keep `.well-known/` excluded |

## Maintenance

Whenever the repository structure, build output, runtime folders, or hosting requirements change, update `.cpanel.yml` and this document in the same task. Treat deployment configuration as part of the application.
