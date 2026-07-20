---
title: No authentication in Version 1
summary: Authentication is intentionally absent. The app assumes a single trusted operator and a host-level access model rather than application logins.
status: accepted
date: 2026-03-02
updated: 2026-07-12
order: 2
---

## Decision

Do not implement user accounts, sessions, or password login in the current application version.

## Why

- The product target is one person capturing ideas.
- Hosting access (cPanel, FTP, SSH) already gates who can reach the files.
- Adding auth early invites incomplete session handling and a false sense of multi-user safety.

## Consequences

- Anyone who can reach the URL can use the app.
- Protect the deployment with host controls or network restrictions if the URL is sensitive.
- Future multi-user support will need ownership validation on every idea record, not only a login screen.

## Related risk

See **Future multi-user without ownership checks**. A login form alone is not enough.
