// Package phpcore discovers the canonical PHP core the vibekb CLI delegates to,
// and runs it.
//
// VibeKB's model loader, validator, static generator, and self-maintenance CLI
// live in PHP (guide/lib and tools/) and are the single source of truth. The Go
// front-end never re-implements them; it locates them and hands off, preserving
// exactly one implementation of every model-semantic operation.
package phpcore

import (
	"os"
	"os/exec"
	"path/filepath"
	"strings"
)

// Runtime describes the discovered PHP core and the repository it belongs to.
type Runtime struct {
	// PHP is the resolved php executable ("" if none was found).
	PHP string
	// PHPVersion is the reported version string, e.g. "8.2.17" ("" if unknown).
	PHPVersion string
	// RepoRoot is the nearest ancestor directory that holds a VibeKB workspace
	// or its tooling ("" if the caller is not inside one).
	RepoRoot string
	// ToolsScript is the repo-relative path to the self-maintenance CLI:
	// ".vibekb/runtime/tools/vibekb.php" for a consolidated install, or the
	// legacy "tools/vibekb.php". "" if none is present.
	ToolsScript string
}

// Discover resolves PHP and the surrounding VibeKB repository from the current
// working directory.
func Discover() Runtime {
	php, ver := FindPHP()
	root := findUp(markerRepo)
	return Runtime{
		PHP:         php,
		PHPVersion:  ver,
		RepoRoot:    root,
		ToolsScript: locateToolsScript(root),
	}
}

// locateToolsScript returns the repo-relative path to vibekb.php, preferring the
// consolidated location under .vibekb/runtime and falling back to the legacy
// root-level tools/.
func locateToolsScript(root string) string {
	if root == "" {
		return ""
	}
	for _, rel := range []string{
		filepath.Join(".vibekb", "runtime", "tools", "vibekb.php"),
		filepath.Join("tools", "vibekb.php"),
	} {
		if exists(filepath.Join(root, rel)) {
			return rel
		}
	}
	return ""
}

// FindPHP resolves the php executable and its version. The VIBEKB_PHP environment
// variable takes precedence, then the usual names on PATH. The version is "" when
// php cannot be executed.
func FindPHP() (path, version string) {
	candidates := []string{}
	if env := strings.TrimSpace(os.Getenv("VIBEKB_PHP")); env != "" {
		candidates = append(candidates, env)
	}
	candidates = append(candidates, "php", "php8.4", "php8.3", "php8.2")

	for _, c := range candidates {
		resolved, err := exec.LookPath(c)
		if err != nil {
			// VIBEKB_PHP may be an absolute path that LookPath rejects if it is
			// not on PATH; accept it if it is executable.
			if filepath.IsAbs(c) {
				if info, statErr := os.Stat(c); statErr == nil && !info.IsDir() {
					resolved = c
				} else {
					continue
				}
			} else {
				continue
			}
		}
		return resolved, phpVersion(resolved)
	}
	return "", ""
}

// phpVersion returns PHP_VERSION as reported by the interpreter, or "".
func phpVersion(php string) string {
	out, err := exec.Command(php, "-r", "echo PHP_VERSION;").Output()
	if err != nil {
		return ""
	}
	return strings.TrimSpace(string(out))
}

// Delegate runs `php <script> <args...>` with the repository as the working
// directory and stdio wired straight through, returning the child's exit code.
// script is repo-relative (e.g. "tools/vibekb.php").
func (r Runtime) Delegate(script string, args []string) int {
	return r.run(r.RepoRoot, script, args)
}

func (r Runtime) run(dir, script string, args []string) int {
	full := filepath.Join(dir, script)
	cmdArgs := append([]string{full}, args...)
	cmd := exec.Command(r.PHP, cmdArgs...)
	cmd.Dir = dir
	cmd.Stdin = os.Stdin
	cmd.Stdout = os.Stdout
	cmd.Stderr = os.Stderr
	if err := cmd.Run(); err != nil {
		if exit, ok := err.(*exec.ExitError); ok {
			return exit.ExitCode()
		}
		return 1
	}
	return 0
}

// markerRepo reports whether dir is (or contains) a VibeKB workspace or tooling.
func markerRepo(dir string) bool {
	return exists(filepath.Join(dir, "tools", "vibekb.php")) ||
		exists(filepath.Join(dir, ".vibekb", "runtime", "tools", "vibekb.php")) ||
		isDir(filepath.Join(dir, ".vibekb"))
}

// findUp walks upward from the working directory to the filesystem root,
// returning the first ancestor matching pred, or "".
func findUp(pred func(string) bool) string {
	dir, err := os.Getwd()
	if err != nil {
		return ""
	}
	for {
		if pred(dir) {
			return dir
		}
		parent := filepath.Dir(dir)
		if parent == dir {
			return ""
		}
		dir = parent
	}
}

func exists(p string) bool {
	info, err := os.Stat(p)
	return err == nil && !info.IsDir()
}

func isDir(p string) bool {
	info, err := os.Stat(p)
	return err == nil && info.IsDir()
}
