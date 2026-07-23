---
id: current-work
type: work
title: Homepage Windows install requirements honesty
objective: Stop implying Windows is unsupported when release binaries already ship; clarify curl installer is macOS/Linux while Windows installs via GitHub Releases.
summary: Complete. Homepage and README name Windows as a manual Releases download path; curl | sh remains macOS/Linux-only. install.sh unchanged.
requested_by: User (mobile screenshot of #compatibility)
status: complete
verification_state: verified-from-source
updated: 2026-07-23
affected_functionality: [run-the-developer-cli, install-into-a-repository, initialize-in-a-repository, deploy-and-stay-portable]
expected_files: [index.php, README.md, .vibekb/memory/changes/homepage-windows-install-copy.md, .vibekb/functionality/records/run-the-developer-cli.md, .vibekb/functionality/records/install-into-a-repository.md, .vibekb/functionality/records/initialize-in-a-repository.md, .vibekb/functionality/records/deploy-and-stay-portable.md, .vibekb/files/important-files.json, .vibekb/work/handoff.md, .vibekb/work/current.md]
data_impact: None — marketing/onboarding copy only; installer and release assets unchanged.
risks:
  - Do not claim a Windows curl/PowerShell installer exists; only manual .exe download is supported.
  - Do not imply Winget or Authenticode signing.
---

## Status

Complete. Homepage Compatibility & Requirements list Windows; install step 1
points Windows users at GitHub Releases. Verified by rendering `index.php` and
checking for the new strings.
