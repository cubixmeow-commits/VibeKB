# Agent instructions — VibeKB

## Deployment Maintenance

Whenever the repository structure changes, new runtime folders are added, the build output changes, or deployment requirements change, automatically review and update `.cpanel.yml` as part of the same task.

The deployment configuration is considered part of the application and must always remain accurate.

Never assume the existing deployment file is still correct.

### Checklist when changing the project layout

1. Confirm the public entry point and whether a production build output directory exists.
2. Update rsync `--exclude` rules for new development-only paths.
3. Exclude or protect new persistent production paths (uploads, storage, databases, sessions, server-only config) so `rsync --delete` cannot remove them.
4. Do not add unsupported shared-hosting build steps (for example assuming Node.js on cPanel) unless verified.
5. Keep secrets (`.env`, local config) out of the deployment sync.
6. Update `DEPLOYMENT.md` so documented exclusions and persistent paths match `.cpanel.yml`.
