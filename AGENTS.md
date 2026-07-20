# Agent guidelines

## Deployment Maintenance

Whenever the repository structure changes, new runtime folders are added, the build output changes, or deployment requirements change, automatically review and update `.cpanel.yml` as part of the same task.

The deployment configuration is considered part of the application and must always remain accurate.

Never assume the existing deployment file is still correct.

### Related files

- `.cpanel.yml` — cPanel Git Version Control deploy tasks (rsync to `/home/iainmcok/public_html/vibekb/`)
- `DEPLOYMENT.md` — human-readable deployment documentation

### Checklist when structure changes

1. Re-identify the public entry point and any production build output directory.
2. Update rsync excludes for new development-only paths.
3. Protect any new persistent production directories from `rsync --delete`.
4. Keep secrets (`.env`, credentials, local config) out of the deploy set.
5. Do not add server-side Node/Composer build steps unless the host is confirmed to support them.
6. Update `DEPLOYMENT.md` to match the new deploy behavior.
