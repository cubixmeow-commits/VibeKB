// Package buildinfo carries the vibekb CLI version.
package buildinfo

// Version is the vibekb CLI version. The default marks an unreleased build; a
// release overrides it at link time, e.g.:
//
//	go build -ldflags "-X github.com/cubixmeow-commits/vibekb/internal/buildinfo.Version=1.2.3"
var Version = "0.1.0-dev"
