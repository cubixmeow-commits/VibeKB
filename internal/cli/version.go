package cli

import (
	"fmt"

	"github.com/cubixmeow-commits/vibekb/internal/buildinfo"
	"github.com/cubixmeow-commits/vibekb/internal/phpcore"
)

func cmdVersion() int {
	rt := phpcore.Discover()
	fmt.Printf("vibekb %s\n", buildinfo.Version)
	if rt.PHP != "" {
		fmt.Printf("php     %s (%s)\n", orUnknown(rt.PHPVersion), rt.PHP)
	} else {
		fmt.Printf("php     not found\n")
	}
	if rt.RepoRoot != "" {
		fmt.Printf("repo    %s\n", rt.RepoRoot)
	}
	return 0
}

func orUnknown(s string) string {
	if s == "" {
		return "unknown version"
	}
	return s
}
