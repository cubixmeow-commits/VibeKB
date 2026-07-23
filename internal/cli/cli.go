// Package cli implements the vibekb command surface: a small native core
// (version, doctor, help) plus thin delegation to the canonical PHP tooling for
// every model-semantic operation.
package cli

import (
	"fmt"
	"os"

	"github.com/cubixmeow-commits/vibekb/internal/installer"
	"github.com/cubixmeow-commits/vibekb/internal/phpcore"
)

// delegatedCommands are the subcommands whose behaviour is owned by the PHP
// self-maintenance CLI. The script's location is discovered at runtime (it moved
// under .vibekb/runtime/tools in consolidated installs), so we only track the
// command names here; keeping one PHP implementation avoids a second, drift-prone
// model loader in Go.
var delegatedCommands = map[string]bool{
	"status":    true,
	"check":     true,
	"affected":  true,
	"bootstrap": true,
	"validate":  true,
	"generate":  true,
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
		// Fully native: copies embedded payload and scaffolds .vibekb/ with no PHP.
		return installer.Run(rest)
	case "uninstall":
		return installer.Uninstall(rest)
	case "migrate":
		return installer.Migrate(rest)
	}

	if delegatedCommands[cmd] {
		return delegate(cmd, rest)
	}

	fmt.Fprintf(os.Stderr, "vibekb: unknown command %q\n\n", cmd)
	printHelp(os.Stderr)
	return 2
}

// delegate forwards a model-semantic subcommand to the PHP self-maintenance CLI,
// after confirming the environment can run it.
func delegate(subcommand string, rest []string) int {
	rt := phpcore.Discover()
	if rt.RepoRoot == "" || rt.ToolsScript == "" {
		fmt.Fprintln(os.Stderr, "vibekb: not inside a VibeKB repository (no .vibekb/runtime/tools/vibekb.php or tools/vibekb.php found above the current directory).")
		fmt.Fprintln(os.Stderr, "Run this from a repository where VibeKB is installed, or run `vibekb install` first.")
		return 1
	}
	if rt.PHP == "" {
		reportMissingPHP(subcommand)
		return 1
	}
	// The PHP CLI expects its own subcommand as the first argument.
	return rt.Delegate(rt.ToolsScript, append([]string{subcommand}, rest...))
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

Native install (no PHP required):
  install [target]    Install VibeKB into a repository (everything under .vibekb/).
  migrate [target]    Consolidate a legacy root-level install under .vibekb/.
  uninstall [target]  Remove VibeKB-owned files and managed blocks safely.

Repository-safety: VibeKB owns only .vibekb/ plus namespaced adapters and clearly
marked managed blocks. See docs/REPOSITORY_SAFETY.md.

Run 'vibekb doctor' first if a delegated command reports a missing runtime.
See ARCHITECTURE.md for how the Go front-end and the PHP core fit together.
`)
}
