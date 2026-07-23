// Command vibekb is the developer entry point for VibeKB.
//
// It is a thin, portable front-end that gives developers one professional
// command (`vibekb`) with a real installation story (a single static binary via
// brew / winget / curl). It deliberately does NOT re-implement the VibeKB
// content model: model parsing, validation, and HTML generation have exactly one
// canonical implementation — the PHP core under guide/lib and tools/. For those
// operations the CLI discovers PHP and delegates, so there is never a second,
// drift-prone model loader to keep in sync.
//
// Commands that are genuinely language-independent (environment diagnostics,
// version, the developer UX) are native Go and need no PHP at all.
//
// See ARCHITECTURE.md for the full assessment and the staged roadmap.
package main

import (
	"os"

	"github.com/cubixmeow-commits/vibekb/internal/cli"
)

func main() {
	os.Exit(cli.Run(os.Args[1:]))
}
