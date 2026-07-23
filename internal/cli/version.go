package cli

import (
	"fmt"
	"runtime"

	"github.com/cubixmeow-commits/vibekb/internal/buildinfo"
	"github.com/cubixmeow-commits/vibekb/internal/phpcore"
)

func cmdVersion() int {
	fmt.Println("VibeKB")
	fmt.Printf("Version: %s\n", buildinfo.Version)
	fmt.Printf("Commit: %s\n", buildinfo.Commit)
	fmt.Printf("Built: %s\n", buildinfo.Built)
	fmt.Printf("Platform: %s/%s\n", runtime.GOOS, runtime.GOARCH)

	rt := phpcore.Discover()
	if rt.PHP != "" {
		fmt.Printf("PHP: %s (%s)\n", orUnknown(rt.PHPVersion), rt.PHP)
	} else {
		fmt.Println("PHP: not found")
	}
	if rt.RepoRoot != "" {
		fmt.Printf("Repo: %s\n", rt.RepoRoot)
	}
	return 0
}

func orUnknown(s string) string {
	if s == "" {
		return "unknown version"
	}
	return s
}
