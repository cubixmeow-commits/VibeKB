package buildinfo

import (
	"strings"
	"testing"
)

// TestDevVersionDefault locks the unstamped development default to the current
// product line so a forgotten bump cannot ship with a stale Version string.
func TestDevVersionDefault(t *testing.T) {
	const wantPrefix = "0.2.0"
	if Version != wantPrefix+"-dev" {
		t.Fatalf("buildinfo.Version = %q, want %q", Version, wantPrefix+"-dev")
	}
	if strings.HasPrefix(Version, "0.1.0") {
		t.Fatalf("stale product version still in buildinfo: %q", Version)
	}
}
