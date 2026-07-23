// Package buildinfo carries link-time identity for the vibekb CLI.
//
// Release builds override Version, Commit, and Built via -ldflags, e.g.:
//
//	go build -ldflags "-X github.com/cubixmeow-commits/vibekb/internal/buildinfo.Version=0.2.0 \
//	  -X github.com/cubixmeow-commits/vibekb/internal/buildinfo.Commit=84c81d2 \
//	  -X github.com/cubixmeow-commits/vibekb/internal/buildinfo.Built=2026-07-23"
//
// Development builds keep the defaults below.
package buildinfo

// Version is the vibekb CLI version (semver without a leading "v").
var Version = "0.2.0-dev"

// Commit is the short git SHA the binary was built from ("unknown" in local builds).
var Commit = "unknown"

// Built is the UTC build date (YYYY-MM-DD), or "dev" for local builds.
var Built = "dev"
