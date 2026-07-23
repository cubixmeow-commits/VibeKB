# RELEASE.md — Publishing a vibekb CLI release

This repository publishes **downloadable, cross-platform `vibekb` binaries**
from GitHub Releases. Tagging a version runs
[`.github/workflows/release.yml`](./.github/workflows/release.yml): it tests,
cross-compiles six platforms with production ldflags, writes `checksums.txt`,
and creates a GitHub Release with auto-generated notes.

The **product install entry point** is the website script
[`install.sh`](./install.sh) at `https://iainreid.dev/vibekb/install.sh` (also
available as `/install`). It downloads the matching asset from the latest GitHub
Release. Homebrew and Winget remain later milestones. Code signing (Apple
notarization, Windows Authenticode) is the recommended next hardening step.

## Publish v0.1.0

On `main`, with a clean tree and CI green:

```bash
git checkout main
git pull origin main

# Optional: smoke a release-shaped local build before tagging
PKG=github.com/cubixmeow-commits/vibekb/internal/buildinfo
go test ./...
CGO_ENABLED=0 go build -trimpath \
  -ldflags "-s -w -X ${PKG}.Version=0.1.0 -X ${PKG}.Commit=$(git rev-parse --short HEAD) -X ${PKG}.Built=$(date -u +%Y-%m-%d)" \
  -o vibekb ./cmd/vibekb
./vibekb version

git tag -a v0.1.0 -m "vibekb 0.1.0"
git push origin v0.1.0
```

Then open the Actions run for **Release vibekb CLI** and, when it finishes, the
[Releases](https://github.com/cubixmeow-commits/VibeKB/releases) page for
`v0.1.0`.

## Release artifacts

Each tag produces:

| File | Platform |
|------|----------|
| `vibekb-windows-amd64.exe` | Windows x86_64 |
| `vibekb-windows-arm64.exe` | Windows ARM64 |
| `vibekb-darwin-amd64` | macOS Intel |
| `vibekb-darwin-arm64` | macOS Apple Silicon |
| `vibekb-linux-amd64` | Linux x86_64 |
| `vibekb-linux-arm64` | Linux ARM64 |
| `checksums.txt` | SHA256 of every binary above |

Binaries are built with `CGO_ENABLED=0`, `-trimpath`, and `-ldflags "-s -w …"`
so they are static where the Go toolchain allows, stripped, and stamped with
`Version`, `Commit`, and `Built` for `vibekb version`.

## End-user install (primary)

```bash
curl -fsSL https://iainreid.dev/vibekb/install.sh | sh
cd /path/to/your/project
vibekb install .
```

The website script detects macOS/Linux + arm64/amd64, downloads the matching
asset from the latest GitHub Release, installs to `/usr/local/bin` or
`~/.local/bin`, and runs `vibekb version`.

### Manual install (secondary)

1. Download the matching binary from the Release.
2. Rename to `vibekb` (or `vibekb.exe` on Windows) and place it on your `PATH`.
3. On macOS/Linux: `chmod +x vibekb`.
4. Run `vibekb install /path/to/your/project`.

PHP 8.2+ is required **after** install for the guide and model commands.

Verify downloads:

```bash
sha256sum -c checksums.txt
# or: shasum -a 256 -c checksums.txt
```

## Remaining manual / future steps

- **Code signing** (next recommended milestone): Apple notarization for macOS
  binaries; Authenticode for Windows. Without this, first launches may show OS
  security prompts (Gatekeeper / SmartScreen). Checksum verification can be
  added to `install.sh` without changing the curl command users remember.
- **Package managers** (later): Homebrew tap, Winget manifest — the website
  install URL stays the default path.
- First-time GitHub Actions release needs the default `GITHUB_TOKEN` write
  permission for `contents` (already set on the workflow). No extra secrets are
  required until code signing is added.
