package cli

import (
	"fmt"
	"os"
	"os/exec"
	"path/filepath"
	"strconv"
	"strings"

	"github.com/cubixmeow-commits/vibekb/internal/installer"
	"github.com/cubixmeow-commits/vibekb/internal/phpcore"
)

// minPHPMajor / minPHPMinor is VibeKB's runtime floor: PHP 8.2.
const (
	minPHPMajor = 8
	minPHPMinor = 2
)

// cmdDoctor runs environment diagnostics natively (no PHP needed) and returns a
// non-zero code when a hard requirement for the delegated commands is missing.
func cmdDoctor() int {
	rt := phpcore.Discover()
	fmt.Println("vibekb doctor — environment check")
	fmt.Println(strings.Repeat("=", 60))

	ok := true

	// PHP: required for every model-semantic command.
	switch {
	case rt.PHP == "":
		reportFail("PHP 8.2+", "not found on PATH — needed by the model loader and generator")
		fmt.Println("      Install PHP 8.2+ or set VIBEKB_PHP to its path.")
		ok = false
	case !phpMeetsFloor(rt.PHPVersion):
		reportFail("PHP 8.2+", fmt.Sprintf("found %s at %s — below the 8.2 floor", orUnknown(rt.PHPVersion), rt.PHP))
		ok = false
	default:
		reportOK("PHP 8.2+", fmt.Sprintf("%s (%s)", rt.PHPVersion, rt.PHP))
	}

	// git: required for drift detection; the model still loads without it.
	if git, err := exec.LookPath("git"); err == nil {
		reportOK("git", git)
	} else {
		reportWarn("git", "not found — drift detection (`check`, `status`) is limited without it")
	}

	// Repository context. `vibekb install` is native and works from anywhere, so
	// this only reports whether the current directory is already a VibeKB repo.
	if rt.RepoRoot != "" {
		reportOK("VibeKB repository", rt.RepoRoot)
		if info, err := os.Stat(filepath.Join(rt.RepoRoot, ".vibekb")); err == nil && info.IsDir() {
			reportOK(".vibekb/ workspace", "present")
		} else {
			reportWarn(".vibekb/ workspace", "not present here — run 'vibekb bootstrap' to scaffold it")
		}
	} else {
		reportWarn("VibeKB repository", "not detected here — 'vibekb install <target>' can create one")
	}

	// Repository footprint diagnostics (native; no PHP).
	if rt.RepoRoot != "" {
		fmt.Println()
		fmt.Println("Repository footprint")
		fmt.Println(strings.Repeat("-", 60))
		for _, f := range installer.DiagnoseRepo(rt.RepoRoot) {
			switch f.Severity {
			case installer.SevError:
				reportFail(f.Label, f.Detail)
				ok = false
			case installer.SevWarn:
				reportWarn(f.Label, f.Detail)
			case installer.SevOK:
				reportOK(f.Label, f.Detail)
			default:
				fmt.Printf("  [info] %-22s %s\n", f.Label, f.Detail)
			}
			if f.Fix != "" {
				fmt.Printf("         ↳ %s\n", f.Fix)
			}
		}
	}

	fmt.Println(strings.Repeat("=", 60))
	if ok {
		fmt.Println("RESULT: OK — the environment can run every vibekb command.")
		return 0
	}
	fmt.Println("RESULT: attention needed — see the items above.")
	return 1
}

// phpMeetsFloor reports whether a PHP version string is >= 8.2.
func phpMeetsFloor(version string) bool {
	if version == "" {
		return false
	}
	parts := strings.SplitN(version, ".", 3)
	if len(parts) < 2 {
		return false
	}
	major, err1 := strconv.Atoi(digits(parts[0]))
	minor, err2 := strconv.Atoi(digits(parts[1]))
	if err1 != nil || err2 != nil {
		return false
	}
	if major != minPHPMajor {
		return major > minPHPMajor
	}
	return minor >= minPHPMinor
}

// digits keeps the leading run of ASCII digits (defensive against suffixes).
func digits(s string) string {
	for i := 0; i < len(s); i++ {
		if s[i] < '0' || s[i] > '9' {
			return s[:i]
		}
	}
	return s
}

func reportOK(label, detail string)   { fmt.Printf("  [ ok ] %-22s %s\n", label, detail) }
func reportWarn(label, detail string) { fmt.Printf("  [warn] %-22s %s\n", label, detail) }
func reportFail(label, detail string) { fmt.Printf("  [fail] %-22s %s\n", label, detail) }
