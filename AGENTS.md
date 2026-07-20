# Agent guidelines

## Deployment Maintenance

Whenever the repository structure changes, new runtime folders are added, the build output changes, or deployment requirements change, automatically review and update `.cpanel.yml` as part of the same task.

The deployment configuration is considered part of the application and must always remain accurate.

Never assume the existing deployment file is still correct.

### Checklist when touching deployment-related structure

1. Re-identify the public entry point and any production build output directory.
2. Update rsync `--exclude` rules for new development-only paths.
3. Protect any new persistent production paths (uploads, storage, databases, sessions, server config) from `rsync --delete`.
4. Keep secrets out of the deploy sync (`.env`, local config, credentials).
5. Do not add Node.js or other unsupported build steps for this cPanel shared host unless the host tooling is confirmed.
6. Update `DEPLOYMENT.md` when the deploy path, exclusions, or persistent data assumptions change.
