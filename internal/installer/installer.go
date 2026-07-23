// Package installer performs a fully native, repository-safe VibeKB installation.
//
// It consolidates everything VibeKB owns under the target's .vibekb/ directory
// (runtime, reference docs, prompts, and a fresh model), and integrates with
// shared, user-owned files only through namespaced adapters or a single clearly
// marked managed block. It reads its payload and starter definition from the
// binary's embedded filesystem, so no PHP process is launched and the source
// repository need not exist afterward. PHP is required only later, to run the
// installed guide.
//
// The set of installed files, the integration adapters, and the ownership rules
// all come from template/manifest.json (embedded). See docs/REPOSITORY_SAFETY.md.
package installer

import (
	"fmt"
	"os"
	"path/filepath"
	"strings"

	"github.com/cubixmeow-commits/vibekb/internal/buildinfo"
)

// Run parses arguments and performs an installation, returning a process exit
// code. It never executes PHP.
func Run(args []string) int {
	opts, err := parseArgs(args)
	if err != nil {
		fmt.Fprintln(os.Stderr, "vibekb install: "+err.Error())
		return 2
	}
	if opts.help {
		usage(os.Stdout)
		return 0
	}

	c := newConsole()
	c.banner()

	m, err := loadManifest()
	if err != nil {
		c.errf("Corrupt installer payload: %v", err)
		c.line("This vibekb binary was built without a valid template/manifest.json. Rebuild from a clean checkout.")
		return 1
	}

	target, err := resolveTarget(opts.target)
	if err != nil {
		c.errf("Target directory does not exist: %s", opts.target)
		return 1
	}

	// Refuse to overwrite VibeKB's own self-hosted model.
	if isSelfHostedRepo(target) {
		c.errf("The target is a VibeKB source/self-hosted repository.")
		c.line("VibeKB's own .vibekb/ model must not be replaced by a fresh one.")
		c.line("Install into a DIFFERENT repository:  vibekb install /path/to/your/project")
		return 1
	}

	// Refuse to write into an unrecognized .vibekb/ (a namespaced collision).
	if isDir(filepath.Join(target, ".vibekb")) && !recognizedVibekb(target) && !opts.force {
		c.errf("A .vibekb/ directory already exists here but was not created by VibeKB.")
		c.line("VibeKB will not overwrite it. If it is unrelated, move or rename it first;")
		c.line("if you are certain it is safe to take over, re-run with --force.")
		return 1
	}

	plan, err := buildPlan(m, target, opts)
	if err != nil {
		c.errf("Could not read the embedded payload: %v", err)
		return 1
	}

	// ---- header ------------------------------------------------------------
	c.kv("Target repository", projectName(target)+"  ("+target+")")
	mode := "fresh install"
	if opts.dryRun {
		mode = "DRY RUN (no changes)"
	} else if plan.isUpgrade {
		mode = "upgrade"
	}
	c.kv("Mode", mode)
	c.kv("Installer", "vibekb "+buildinfo.Version+" (native, no PHP required)")
	if prior := readInstallManifest(target, m.manifestPath()); prior != nil && prior.TemplateVersion != "" {
		c.kv("Installed version", prior.TemplateVersion+"  →  "+m.TemplateVersion)
	}
	c.blank()

	if plan.legacyDetected {
		c.warn("A pre-2.0 (root-level) VibeKB install was detected in this repository.")
		c.line("  This installer consolidates everything under .vibekb/. To relocate the old")
		c.line("  root-level files safely, run:  vibekb migrate .  (preview with --dry-run)")
		c.blank()
	}

	// ---- repository sanity check -------------------------------------------
	if signals := repoSignals(target); len(signals) == 0 {
		c.warn("This directory does not look like a software project:")
		c.line("  - no .git, no common source folders, no README.")
		if !opts.yes && !opts.dryRun && !c.confirm("Install VibeKB here anyway?", false) {
			c.line("Aborted.")
			return 1
		}
	} else {
		c.kv("Detected", strings.Join(signals, ", "))
	}

	renderPlan(c, m, plan, opts)

	if opts.dryRun {
		c.blank()
		c.ok("Dry run complete. No files were changed.")
		return 0
	}

	if !opts.yes {
		c.blank()
		if !c.confirm("Install VibeKB?", true) {
			c.line("Aborted. Nothing was changed.")
			return 1
		}
	}

	// ---- apply -------------------------------------------------------------
	c.blank()
	c.section("Installing")
	res, ok := applyPlan(c, m, target, plan)
	if !ok {
		return 1
	}
	c.ok(fmt.Sprintf("Wrote %d runtime/reference file(s) under .vibekb/.", res.copiedFiles))
	for _, a := range plan.adapters {
		reportAdapter(c, a)
	}
	for _, cf := range res.conflicts {
		c.warn("Integration skipped (conflict): " + cf)
		c.line("    Existing VibeKB markers are malformed or duplicated. Fix them by hand, then re-run.")
	}
	if len(res.backups) > 0 {
		c.line(fmt.Sprintf("Backed up %d shared file(s) under .vibekb/backups/ before editing.", len(res.backups)))
	}

	// ---- fresh model (or preserve) -----------------------------------------
	c.section("Preparing the .vibekb/ model")
	if plan.workspacePreset {
		c.line("An existing .vibekb/ model was found — preserving it (use --force to reset it).")
		rep := scaffoldWorkspace(m, target, false)
		if len(rep.errors) > 0 {
			for _, e := range rep.errors {
				c.errf("%s", e)
			}
			return 1
		}
		if len(rep.createdDirs) > 0 || len(rep.createdFiles) > 0 {
			c.line(fmt.Sprintf("Repaired %d dir(s) and %d missing file(s).", len(rep.createdDirs), len(rep.createdFiles)))
		}
	} else {
		rep := scaffoldWorkspace(m, target, opts.force && isDir(filepath.Join(target, ".vibekb")))
		if len(rep.errors) > 0 {
			for _, e := range rep.errors {
				c.errf("%s", e)
			}
			return 1
		}
		c.ok(fmt.Sprintf("Scaffolded a fresh, empty model (%d dirs, %d files).",
			len(rep.createdDirs), len(rep.createdFiles)+len(rep.overwritten)))
	}

	// ---- record the installation manifest (last) ---------------------------
	prior := readInstallManifest(target, m.manifestPath())
	if err := writeInstallManifest(target, m, res.files, prior); err != nil {
		c.warn("Could not write " + m.manifestPath() + ": " + err.Error())
	}

	// ---- verify (native — no PHP) ------------------------------------------
	c.section("Verifying installation")
	verified := verify(c, m, target)

	c.blank()
	if verified {
		c.ok("Installation complete.")
	} else {
		c.warn("Installation finished with warnings — see above.")
	}
	nextSteps(c, target)
	if verified {
		return 0
	}
	return 1
}

func resolveTarget(t string) (string, error) {
	if t == "" {
		if cwd, err := os.Getwd(); err == nil {
			t = cwd
		}
	}
	abs, err := filepath.Abs(t)
	if err != nil || !isDir(abs) {
		return "", fmt.Errorf("not a directory")
	}
	return abs, nil
}

// ---- options ---------------------------------------------------------------

type options struct {
	target                            string
	dryRun, yes, force, upgrade, help bool
	knowledgeOnly, noIntegrations     bool
	integrate                         []string
	integrateSet                      bool
}

func parseArgs(args []string) (options, error) {
	o := options{}
	for i := 0; i < len(args); i++ {
		a := args[i]
		switch {
		case a == "--dry-run":
			o.dryRun = true
		case a == "--yes" || a == "-y":
			o.yes = true
		case a == "--force":
			o.force = true
		case a == "--upgrade":
			o.upgrade = true
		case a == "--help" || a == "-h":
			o.help = true
		case a == "--knowledge-only":
			o.knowledgeOnly = true
		case a == "--no-integrations":
			o.noIntegrations = true
		case a == "--integrate":
			o.integrateSet = true
			if i+1 < len(args) && !strings.HasPrefix(args[i+1], "-") {
				o.integrate = append(o.integrate, splitList(args[i+1])...)
				i++
			}
		case strings.HasPrefix(a, "--integrate="):
			o.integrateSet = true
			o.integrate = append(o.integrate, splitList(strings.TrimPrefix(a, "--integrate="))...)
		case strings.HasPrefix(a, "-"):
			return o, fmt.Errorf("unknown option: %s", a)
		default:
			if o.target == "" {
				o.target = a
			}
		}
	}
	return o, nil
}

func splitList(s string) []string {
	var out []string
	for _, p := range strings.Split(s, ",") {
		if p = strings.TrimSpace(p); p != "" {
			out = append(out, p)
		}
	}
	return out
}

func usage(w *os.File) {
	fmt.Fprint(w, `vibekb install — prepare a repository for VibeKB (native, no PHP required).

  vibekb install [options] [target]

  target            Directory to install into (default: current directory).

Everything VibeKB owns is written under .vibekb/. Files outside it are optional,
namespaced adapters or a single clearly marked managed block — VibeKB never owns
or overwrites your existing repository files.

Options:
  --knowledge-only  Install only the authoritative .vibekb/ system; touch no
                    integration files outside it.
  --no-integrations Alias for --knowledge-only.
  --integrate LIST  Install only the named adapters (comma-separated), creating
                    them even if their tool is not detected. Known adapters:
                    cursor, copilot, agents, claude.
  --dry-run         Show every proposed change; write nothing.
  --force           Permit taking over an unrecognized .vibekb/ and reset the
                    model. Never overwrites shared files wholesale, and never
                    touches anything outside .vibekb/ and the declared adapters.
  --upgrade         Refresh the VibeKB runtime and preserve the model
                    (auto-detected when a prior install exists).
  --yes, -y         Assume "yes" to prompts (non-interactive).
  --help, -h        This help.

By default (plain `+"`vibekb install .`"+`) VibeKB installs the .vibekb/ system and,
where they are already in use, namespaced adapters; it inserts a managed block
into an existing AGENTS.md/CLAUDE.md only if that file already exists. See
docs/REPOSITORY_SAFETY.md.
`)
}

// ---- rendering -------------------------------------------------------------

func renderPlan(c *console, m manifest, p plan, opts options) {
	c.section("Plan")

	// Payload, grouped by top-level dest directory.
	counts := map[action]map[string]int{actionCreate: {}, actionReplace: {}, actionSkip: {}}
	for _, op := range p.payload {
		top := strings.SplitN(op.dest, "/", 3)
		key := top[0]
		if len(top) > 1 {
			key = top[0] + "/" + top[1]
		}
		counts[op.action][key]++
	}
	labels := map[action]string{actionCreate: "Create", actionReplace: "Replace", actionSkip: "Skip"}
	for _, act := range []action{actionCreate, actionReplace} {
		byDir := counts[act]
		if len(byDir) == 0 {
			continue
		}
		c.line(labels[act] + " (VibeKB-owned, under .vibekb/):")
		for _, top := range sortedKeys(byDir) {
			c.line(fmt.Sprintf("  %s/  (%d file(s))", top, byDir[top]))
		}
	}

	// Model.
	c.line("Model:")
	if p.workspacePreset {
		c.line("  .vibekb/  — preserve (existing model kept)")
	} else {
		c.line("  .vibekb/  — create (fresh empty model)")
	}

	// Integrations.
	if opts.knowledgeOnly || opts.noIntegrations {
		c.line("Integrations: none (knowledge-only).")
	} else if len(p.adapters) == 0 {
		c.line("Integrations: none selected (no tools detected; use --integrate to add).")
	} else {
		c.line("Integrations (optional adapters outside .vibekb/):")
		for _, a := range p.adapters {
			c.line("  " + describeAdapter(a))
		}
	}

	if opts.dryRun {
		c.blank()
		c.line("Full file list:")
		for _, op := range p.payload {
			c.line(fmt.Sprintf("  %-8s %s", strings.ToUpper(op.action.label()), op.dest))
		}
		for _, a := range p.adapters {
			c.line(fmt.Sprintf("  %-8s %s", strings.ToUpper(string(adapterVerb(a))), a.dest))
		}
	}
}

func describeAdapter(a adapterOp) string {
	switch a.kind {
	case "namespaced":
		verb := "create"
		if a.preExisting {
			verb = "replace"
		}
		return fmt.Sprintf("%s  → %s  (namespaced, VibeKB-owned)", a.dest, verb)
	case "managed-block":
		switch a.outcome.Action {
		case blockInsert:
			return fmt.Sprintf("%s  → insert managed block  (shared file preserved)", a.dest)
		case blockUpdate:
			return fmt.Sprintf("%s  → update managed block  (shared file preserved)", a.dest)
		case blockNoop:
			return fmt.Sprintf("%s  → managed block already current  (no change)", a.dest)
		case blockConflict:
			return fmt.Sprintf("%s  → CONFLICT: %s  (skipped)", a.dest, a.outcome.Detail)
		}
	}
	return a.dest
}

func adapterVerb(a adapterOp) blockAction {
	if a.kind == "managed-block" {
		return a.outcome.Action
	}
	if a.preExisting {
		return "replace"
	}
	return "create"
}

func reportAdapter(c *console, a adapterOp) {
	switch a.kind {
	case "namespaced":
		c.ok("Adapter " + a.name + ": wrote " + a.dest)
	case "managed-block":
		switch a.outcome.Action {
		case blockInsert:
			c.ok("Adapter " + a.name + ": inserted managed block into " + a.dest)
		case blockUpdate:
			c.ok("Adapter " + a.name + ": updated managed block in " + a.dest)
		case blockNoop:
			c.line("  Adapter " + a.name + ": managed block already current in " + a.dest)
		}
	}
}

// ---- verification (native) -------------------------------------------------

func verify(c *console, m manifest, target string) bool {
	ok := true
	checks := []struct{ rel, label string }{
		{".vibekb/runtime/guide/index.php", "guide (the dynamic app)"},
		{".vibekb/runtime/tools/vibekb.php", "tools (the self-maintenance CLI)"},
		{".vibekb/prompts/INTEGRATE_VIBEKB.md", "prompts (the integration prompt)"},
		{".vibekb/reference/WORKFLOW.md", "reference (operating rules)"},
		{m.starterDefInstalled() + "/starter.json", "starter definition (for bootstrap/repair)"},
		{".vibekb/manifest.json", "starter model"},
		{m.manifestPath(), "installation manifest"},
	}
	for _, ck := range checks {
		if fileExists(filepath.Join(target, filepath.FromSlash(ck.rel))) {
			c.ok(ck.label + " present")
		} else {
			c.errf("missing: %s (%s)", ck.label, ck.rel)
			ok = false
		}
	}
	c.line("    (run `vibekb check` — which needs PHP — to validate the model itself.)")
	return ok
}

func nextSteps(c *console, target string) {
	name := projectName(target)
	c.blank()
	c.section("Next steps")
	c.line("VibeKB is installed but the model is empty — that is by design. The")
	c.line("installer prepares the workspace; an AI coding agent builds the model.")
	c.blank()
	c.line("  1. Open " + name + " in your coding agent (Claude Code, Cursor, Codex, …).")
	c.line("  2. Ask it to:")
	c.line("       Build the first VibeKB model for this repository using")
	c.line("       .vibekb/prompts/INTEGRATE_VIBEKB.md")
	c.line("  3. When it finishes (PHP 8.2+ needed):  vibekb check")
	c.line("       (or:  php .vibekb/runtime/tools/vibekb.php check)")
	c.blank()
	c.line("Everything VibeKB owns lives under .vibekb/. Remove it any time with:")
	c.line("  vibekb uninstall " + name)
}
