// Package cli implements the vibekb command surface: a small native core
// (version, doctor, help) plus thin delegation to the canonical PHP tooling for
// every model-semantic operation.
package cli

import (
	"fmt"
	"os"

	"github.com/cubixmeow-commits/vibekb/internal/phpcore"
)

// delegatedCommands map a vibekb subcommand to the PHP script (relative to the
// repository root) that owns its behaviour. These are the operations that touch
// the content model; keeping one PHP implementation avoids a second, drift-prone
// model loader in Go.
var delegatedCommands = map[string]string{
	"status":    "tools/vibekb.php",
	"check":     "tools/vibekb.php",
	"affected":  "tools/vibekb.php",
	"bootstrap": "tools/vibekb.php",
	"validate":  "tools/vibekb.php",
	"generate":  "tools/vibekb.php",
}

// Run dispatches a vibekb invocation and returns a process exit code.
func Run(args []string) int {
	if len(args) == 0 {
		printHelp(os.Stdout)
		return 0
	}

	cmd, rest := args[0], args[1:]
	switch cmd {
	case "help", "--help", "-h":
		printHelp(os.Stdout)
		return 0
	case "version", "--version", "-v":
		return cmdVersion()
	case "doctor":
		return cmdDoctor()
	case "install":
		return delegateInstall(rest)
	}

	if script, ok := delegatedCommands[cmd]; ok {
		return delegate(cmd, script, rest)
	}

	fmt.Fprintf(os.Stderr, "vibekb: unknown command %q\n\n", cmd)
	printHelp(os.Stderr)
	return 2
}

// delegate forwards a model-semantic subcommand to the PHP self-maintenance CLI,
// after confirming the environment can run it.
func delegate(subcommand, script string, rest []string) int {
	rt := phpcore.Discover()
	if rt.RepoRoot == "" {
		fmt.Fprintln(os.Stderr, "vibekb: not inside a VibeKB repository (no tools/vibekb.php or .vibekb/ found above the current directory).")
		fmt.Fprintln(os.Stderr, "Run this from a repository where VibeKB is installed, or run `vibekb install` first.")
		return 1
	}
	if rt.PHP == "" {
		reportMissingPHP(subcommand)
		return 1
	}
	// The PHP CLI expects its own subcommand as the first argument.
	return rt.Delegate(script, append([]string{subcommand}, rest...))
}

// delegateInstall forwards to the PHP installer, which must be run from a VibeKB
// source clone.
func delegateInstall(rest []string) int {
	rt := phpcore.Discover()
	if rt.SourceRoot == "" {
		fmt.Fprintln(os.Stderr, "vibekb: `install` must run from a VibeKB source clone (install.php not found above the current directory).")
		fmt.Fprintln(os.Stderr, "  git clone https://github.com/cubixmeow-commits/VibeKB.git && cd VibeKB")
		fmt.Fprintln(os.Stderr, "  vibekb install /path/to/your/project")
		return 1
	}
	if rt.PHP == "" {
		reportMissingPHP("install")
		return 1
	}
	return rt.DelegateSource("install.php", rest)
}

func reportMissingPHP(subcommand string) {
	fmt.Fprintf(os.Stderr, "vibekb: `%s` needs PHP 8.2+, which was not found on PATH.\n", subcommand)
	fmt.Fprintln(os.Stderr, "VibeKB's model loader and generator run on PHP; the vibekb binary delegates to them.")
	fmt.Fprintln(os.Stderr, "Install PHP 8.2+ (e.g. `brew install php`, `apt install php-cli`) or set VIBEKB_PHP to its path,")
	fmt.Fprintln(os.Stderr, "then run `vibekb doctor` to confirm.")
}

func printHelp(w *os.File) {
	fmt.Fprint(w, `vibekb — the developer CLI for VibeKB

Understand what your software is doing. The vibekb binary is a portable
front-end; model parsing, validation, and site generation run on the canonical
PHP core, which vibekb discovers and delegates to.

Usage:
  vibekb <command> [arguments]

Native commands (no PHP required):
  doctor              Check the environment: PHP, git, and repository state.
  version             Print the vibekb CLI version and detected runtime.
  help                Show this help.

Model commands (delegated to the PHP core):
  status              Session start: provenance, current work, drift summary.
  check [--strict]    Validation + broken references + drift + /docs sync.
  affected <file>...  Map changed files to the functionality they affect.
  bootstrap [--dry-run]  Verify and repair the .vibekb/ workspace.
  validate [path]     Run the headless model validator.
  generate            Regenerate the static /docs snapshot.
  install [target]    Install VibeKB into a repository (from a source clone).

Run 'vibekb doctor' first if a delegated command reports a missing runtime.
See ARCHITECTURE.md for how the Go front-end and the PHP core fit together.
`)
}
