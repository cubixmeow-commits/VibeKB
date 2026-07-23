// Package installer performs a fully native VibeKB installation.
//
// It copies the VibeKB runtime into a target repository and scaffolds a fresh,
// empty-but-valid .vibekb/ workspace — reading its payload and starter definition
// from the binary's embedded filesystem, so no PHP process is launched and the
// source repository need not exist afterward. PHP is required only later, to run
// the installed guide; it is never required to install.
//
// The set of installed files comes from template/manifest.json (embedded), the
// single source of truth shared with the install.php compatibility wrapper. The
// fresh workspace comes from template/starter/ (embedded), the single canonical
// starter definition also read by tools/lib/Starter.php.
package installer

import (
	"bytes"
	"encoding/json"
	"fmt"
	"io/fs"
	"os"
	"path/filepath"
	"sort"
	"strings"
	"time"

	vibekb "github.com/cubixmeow-commits/vibekb"
	"github.com/cubixmeow-commits/vibekb/internal/buildinfo"
)

const sourceRepository = "https://github.com/cubixmeow-commits/VibeKB"

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

	// ---- resolve the target ------------------------------------------------
	target := opts.target
	if target == "" {
		if cwd, err := os.Getwd(); err == nil {
			target = cwd
		}
	}
	target, err = filepath.Abs(target)
	if err != nil || !isDir(target) {
		c.errf("Target directory does not exist: %s", opts.target)
		return 1
	}

	// Refuse to overwrite VibeKB's own self-hosted model.
	if isSelfHostedRepo(target) {
		c.errf("The target is a VibeKB source/self-hosted repository.")
		c.line("VibeKB's own .vibekb/ model must not be replaced by a fresh one.")
		c.line("Install into a DIFFERENT repository:  vibekb install /path/to/your/project")
		c.line("To verify or repair THIS repo's workspace, use:  php tools/vibekb.php bootstrap")
		return 1
	}

	// ---- prior install / mode ----------------------------------------------
	priorState := readState(target)
	hasVibekb := isDir(filepath.Join(target, ".vibekb"))
	isUpgrade := opts.upgrade || priorState != nil

	c.kv("Target repository", projectName(target)+"  ("+target+")")
	mode := "fresh install"
	if opts.dryRun {
		mode = "DRY RUN (no changes)"
	} else if isUpgrade {
		mode = "upgrade"
	}
	c.kv("Mode", mode)
	c.kv("Installer", "vibekb "+buildinfo.Version+" (native, no PHP required)")
	if priorState != nil && priorState.TemplateVersion != "" {
		c.kv("Installed version", priorState.TemplateVersion+"  →  "+m.TemplateVersion)
	}
	c.blank()

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

	// ---- plan --------------------------------------------------------------
	plan, err := buildPlan(m, target, isUpgrade, opts.force)
	if err != nil {
		c.errf("Could not read the embedded payload: %v", err)
		return 1
	}
	renderPlan(c, plan, hasVibekb, opts)

	if len(plan.blocked) > 0 && !opts.force {
		c.blank()
		c.warn(fmt.Sprintf("%d existing file(s) would be overwritten and were SKIPPED for safety.", len(plan.blocked)))
		c.line("Re-run with --force to replace them, or remove/rename them first. Application code is never replaced without --force.")
	}

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

	// ---- copy the payload --------------------------------------------------
	c.blank()
	c.section("Installing runtime")
	copied := 0
	for _, it := range plan.items {
		if it.action != actionCreate && it.action != actionReplace {
			continue
		}
		if err := writeEmbedded(it.embedPath, filepath.Join(target, filepath.FromSlash(it.embedPath))); err != nil {
			c.errf("Failed to copy %s: %v", it.embedPath, err)
			return 1
		}
		copied++
	}
	c.ok(fmt.Sprintf("Copied %d runtime file(s).", copied))

	// ---- fresh model (or preserve) -----------------------------------------
	c.section("Preparing the .vibekb/ workspace")
	if hasVibekb && !opts.force {
		c.line("An existing .vibekb/ was found — preserving it (use --force to reset the model).")
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
		rep := scaffoldWorkspace(m, target, opts.force && hasVibekb)
		if len(rep.errors) > 0 {
			for _, e := range rep.errors {
				c.errf("%s", e)
			}
			return 1
		}
		c.ok(fmt.Sprintf("Scaffolded a fresh, empty model (%d dirs, %d files).",
			len(rep.createdDirs), len(rep.createdFiles)+len(rep.overwritten)))
	}

	// ---- record installer state --------------------------------------------
	if err := writeState(target, m, plan, priorState); err != nil {
		c.warn("Could not write .vibekb/.installer.json: " + err.Error())
	}

	// ---- verify (native — no PHP) ------------------------------------------
	c.section("Verifying installation")
	ok := verify(c, m, target)

	// ---- next steps --------------------------------------------------------
	c.blank()
	if ok {
		c.ok("Installation complete.")
	} else {
		c.warn("Installation finished with warnings — see above.")
	}
	nextSteps(c, target)
	if ok {
		return 0
	}
	return 1
}

// ---- options ---------------------------------------------------------------

type options struct {
	target                            string
	dryRun, yes, force, upgrade, help bool
}

func parseArgs(args []string) (options, error) {
	o := options{}
	for _, a := range args {
		switch a {
		case "--dry-run":
			o.dryRun = true
		case "--yes", "-y":
			o.yes = true
		case "--force":
			o.force = true
		case "--upgrade":
			o.upgrade = true
		case "--help", "-h":
			o.help = true
		default:
			if strings.HasPrefix(a, "-") {
				return o, fmt.Errorf("unknown option: %s", a)
			}
			if o.target == "" {
				o.target = a
			}
		}
	}
	return o, nil
}

func usage(w *os.File) {
	fmt.Fprint(w, `vibekb install — prepare a repository for VibeKB (native, no PHP required).

  vibekb install [options] [target]

  target            Directory to install into (default: current directory).

Options:
  --dry-run         Show what would happen; change nothing.
  --yes, -y         Assume "yes" to prompts (non-interactive).
  --force           Overwrite pre-existing files, including an existing .vibekb/
                    model. Application code is otherwise never overwritten.
  --upgrade         Refresh the VibeKB runtime and preserve .vibekb/
                    (auto-detected when a prior install exists).
  --help, -h        This help.

The installer prepares the workspace from files embedded in the vibekb binary; it
never analyses your application and never runs PHP. An AI coding agent builds the
model afterwards using prompts/INTEGRATE_VIBEKB.md. PHP 8.2+ is needed only to run
the installed guide. See INSTALLER.md.
`)
}

// ---- manifest --------------------------------------------------------------

type manifest struct {
	TemplateVersion string `json:"template_version"`
	Payload         struct {
		Runtime []string `json:"runtime"`
		Agent   []string `json:"agent"`
		Docs    []string `json:"docs"`
	} `json:"payload"`
	Preserve struct {
		Paths []string `json:"paths"`
	} `json:"preserve"`
	StarterModel struct {
		Definition string `json:"definition"`
	} `json:"starter_model"`
}

func (m manifest) payloadPaths() []string {
	var out []string
	out = append(out, m.Payload.Runtime...)
	out = append(out, m.Payload.Agent...)
	out = append(out, m.Payload.Docs...)
	return out
}

func (m manifest) starterDef() string {
	if m.StarterModel.Definition != "" {
		return m.StarterModel.Definition
	}
	return "template/starter"
}

func loadManifest() (manifest, error) {
	var m manifest
	b, err := vibekb.PayloadFS.ReadFile("template/manifest.json")
	if err != nil {
		return m, err
	}
	if err := json.Unmarshal(b, &m); err != nil {
		return m, err
	}
	if len(m.payloadPaths()) == 0 {
		return m, fmt.Errorf("manifest declares no payload")
	}
	return m, nil
}

// ---- planning --------------------------------------------------------------

type action int

const (
	actionCreate action = iota
	actionReplace
	actionSkip
)

func (a action) label() string {
	switch a {
	case actionCreate:
		return "create"
	case actionReplace:
		return "replace"
	default:
		return "skip"
	}
}

type planItem struct {
	embedPath string // repository-root-relative (forward slashes)
	action    action
}

type plan struct {
	items   []planItem
	blocked []string
}

func buildPlan(m manifest, target string, isUpgrade, force bool) (plan, error) {
	var p plan
	for _, payloadPath := range m.payloadPaths() {
		files, err := embedEntries(payloadPath)
		if err != nil {
			// A payload path with no embedded bytes is a build-time omission.
			return p, fmt.Errorf("%s: %w", payloadPath, err)
		}
		for _, rel := range files {
			dst := filepath.Join(target, filepath.FromSlash(rel))
			exists := fileExists(dst)
			var act action
			switch {
			case !exists:
				act = actionCreate
			case isUpgrade || force:
				act = actionReplace
			default:
				act = actionSkip
				p.blocked = append(p.blocked, rel)
			}
			p.items = append(p.items, planItem{embedPath: rel, action: act})
		}
	}
	return p, nil
}

func renderPlan(c *console, p plan, hasVibekb bool, opts options) {
	c.section("Plan")
	counts := map[action]map[string]int{
		actionCreate:  {},
		actionReplace: {},
		actionSkip:    {},
	}
	for _, it := range p.items {
		top := strings.SplitN(it.embedPath, "/", 2)[0]
		counts[it.action][top]++
	}
	labels := map[action]string{actionCreate: "Create", actionReplace: "Replace", actionSkip: "Skip (exists)"}
	for _, act := range []action{actionCreate, actionReplace, actionSkip} {
		byDir := counts[act]
		if len(byDir) == 0 {
			continue
		}
		c.line(labels[act] + ":")
		for _, top := range sortedKeys(byDir) {
			n := byDir[top]
			suffix := "/"
			if n > 1 {
				suffix = fmt.Sprintf("/  (%d files)", n)
			} else if strings.Contains(top, ".") {
				suffix = ""
			}
			c.line("  " + top + suffix)
		}
	}
	c.line("Model:")
	if hasVibekb && !opts.force {
		c.line("  .vibekb/  — preserve (existing model kept)")
	} else {
		c.line("  .vibekb/  — create (fresh empty model)")
	}

	if opts.dryRun {
		c.blank()
		c.line("Full file list:")
		for _, it := range p.items {
			c.line(fmt.Sprintf("  %-8s %s", strings.ToUpper(it.action.label()), it.embedPath))
		}
	}
}

// ---- scaffolding -----------------------------------------------------------

type scaffoldReport struct {
	createdDirs, createdFiles, kept, overwritten []string
	errors                                       []string
}

func scaffoldWorkspace(m manifest, target string, force bool) scaffoldReport {
	var rep scaffoldReport
	vibekbRoot := filepath.Join(target, ".vibekb")
	def := m.starterDef()

	// Directories (including intentionally-empty ones).
	dirs, err := starterDirs(def)
	if err != nil {
		rep.errors = append(rep.errors, "read starter.json: "+err.Error())
		return rep
	}
	if !isDir(vibekbRoot) {
		if err := os.MkdirAll(vibekbRoot, 0o755); err != nil {
			rep.errors = append(rep.errors, "create .vibekb: "+err.Error())
			return rep
		}
		rep.createdDirs = append(rep.createdDirs, ".")
	}
	for _, d := range dirs {
		path := filepath.Join(vibekbRoot, filepath.FromSlash(d))
		if !isDir(path) {
			if err := os.MkdirAll(path, 0o755); err != nil {
				rep.errors = append(rep.errors, "create dir "+d+": "+err.Error())
				continue
			}
			rep.createdDirs = append(rep.createdDirs, d)
		}
	}

	// Files, with token substitution.
	date := time.Now().Format("2006-01-02")
	nameJSON := jsonString(projectName(target))
	filesRoot := def + "/files"
	entries, err := embedEntries(filesRoot)
	if err != nil {
		rep.errors = append(rep.errors, "read starter files: "+err.Error())
		return rep
	}
	for _, embedPath := range entries {
		rel := strings.TrimPrefix(embedPath, filesRoot+"/")
		dst := filepath.Join(vibekbRoot, filepath.FromSlash(rel))
		exists := fileExists(dst)
		if exists && !force {
			rep.kept = append(rep.kept, rel)
			continue
		}
		b, err := vibekb.PayloadFS.ReadFile(embedPath)
		if err != nil {
			rep.errors = append(rep.errors, "read "+rel+": "+err.Error())
			continue
		}
		out := substituteTokens(b, date, nameJSON)
		if err := os.MkdirAll(filepath.Dir(dst), 0o755); err != nil {
			rep.errors = append(rep.errors, "mkdir for "+rel+": "+err.Error())
			continue
		}
		if err := os.WriteFile(dst, out, 0o644); err != nil {
			rep.errors = append(rep.errors, "write "+rel+": "+err.Error())
			continue
		}
		if exists {
			rep.overwritten = append(rep.overwritten, rel)
		} else {
			rep.createdFiles = append(rep.createdFiles, rel)
		}
	}
	return rep
}

func starterDirs(def string) ([]string, error) {
	b, err := vibekb.PayloadFS.ReadFile(def + "/starter.json")
	if err != nil {
		return nil, err
	}
	var s struct {
		Dirs []string `json:"dirs"`
	}
	if err := json.Unmarshal(b, &s); err != nil {
		return nil, err
	}
	var out []string
	for _, d := range s.Dirs {
		if d = strings.TrimSpace(d); d != "" {
			out = append(out, d)
		}
	}
	return out, nil
}

func substituteTokens(b []byte, date, nameJSON string) []byte {
	s := string(b)
	s = strings.ReplaceAll(s, "{{DATE}}", date)
	s = strings.ReplaceAll(s, "{{PROJECT_NAME_JSON}}", nameJSON)
	return []byte(s)
}

// jsonString encodes s as a JSON string literal (quotes included), matching
// PHP's json_encode(..., JSON_UNESCAPED_SLASHES) for typical project names.
func jsonString(s string) string {
	buf := &bytes.Buffer{}
	enc := json.NewEncoder(buf)
	enc.SetEscapeHTML(false)
	_ = enc.Encode(s)
	return strings.TrimRight(buf.String(), "\n")
}

// ---- installer state -------------------------------------------------------

type installerState struct {
	TemplateVersion  string   `json:"template_version"`
	InstalledAt      string   `json:"installed_at"`
	UpdatedAt        string   `json:"updated_at"`
	SourceRepository string   `json:"source_repository"`
	InstalledBy      string   `json:"installed_by"`
	Payload          []string `json:"payload"`
	Note             string   `json:"note"`
}

func readState(target string) *installerState {
	b, err := os.ReadFile(filepath.Join(target, ".vibekb", ".installer.json"))
	if err != nil {
		return nil
	}
	var s installerState
	if json.Unmarshal(b, &s) != nil {
		return nil
	}
	return &s
}

func writeState(target string, m manifest, p plan, prior *installerState) error {
	var installed []string
	for _, it := range p.items {
		if it.action == actionCreate || it.action == actionReplace {
			installed = append(installed, it.embedPath)
		}
	}
	sort.Strings(installed)
	now := time.Now().Format(time.RFC3339)
	installedAt := now
	if prior != nil && prior.InstalledAt != "" {
		installedAt = prior.InstalledAt
	}
	s := installerState{
		TemplateVersion:  m.TemplateVersion,
		InstalledAt:      installedAt,
		UpdatedAt:        now,
		SourceRepository: sourceRepository,
		InstalledBy:      "vibekb " + buildinfo.Version,
		Payload:          installed,
		Note:             "Written by the native vibekb installer. Records which files VibeKB owns in this repository so upgrades can refresh them safely. Do not edit by hand.",
	}
	dir := filepath.Join(target, ".vibekb")
	if err := os.MkdirAll(dir, 0o755); err != nil {
		return err
	}
	b, err := json.MarshalIndent(s, "", "  ")
	if err != nil {
		return err
	}
	return os.WriteFile(filepath.Join(dir, ".installer.json"), append(b, '\n'), 0o644)
}

// ---- verification (native) -------------------------------------------------

func verify(c *console, m manifest, target string) bool {
	ok := true
	checks := []struct{ rel, label string }{
		{"guide/index.php", "guide (the dynamic app)"},
		{"tools/vibekb.php", "tools (the self-maintenance CLI)"},
		{"prompts/INTEGRATE_VIBEKB.md", "prompts (the integration prompt)"},
		{m.starterDef() + "/starter.json", "starter definition (for bootstrap/repair)"},
		{".vibekb/manifest.json", "starter model"},
	}
	for _, ck := range checks {
		if fileExists(filepath.Join(target, filepath.FromSlash(ck.rel))) {
			c.ok(ck.label + " present")
		} else {
			c.errf("missing: %s (%s)", ck.label, ck.rel)
			ok = false
		}
	}

	// Workspace completeness, checked natively against the embedded definition.
	missingDirs, missingFiles, err := workspaceGaps(m, target)
	if err != nil {
		c.warn("could not verify workspace completeness: " + err.Error())
		return ok
	}
	if len(missingDirs) == 0 && len(missingFiles) == 0 {
		c.ok("workspace complete — every starter directory and file is present")
	} else {
		c.warn(fmt.Sprintf("workspace incomplete: %d dir(s), %d file(s) missing", len(missingDirs), len(missingFiles)))
		ok = false
	}
	c.line("    (run `vibekb check` — which needs PHP — to validate the model itself.)")
	return ok
}

func workspaceGaps(m manifest, target string) (missingDirs, missingFiles []string, err error) {
	vibekbRoot := filepath.Join(target, ".vibekb")
	def := m.starterDef()
	dirs, err := starterDirs(def)
	if err != nil {
		return nil, nil, err
	}
	for _, d := range dirs {
		if !isDir(filepath.Join(vibekbRoot, filepath.FromSlash(d))) {
			missingDirs = append(missingDirs, d)
		}
	}
	filesRoot := def + "/files"
	entries, err := embedEntries(filesRoot)
	if err != nil {
		return nil, nil, err
	}
	for _, embedPath := range entries {
		rel := strings.TrimPrefix(embedPath, filesRoot+"/")
		if !fileExists(filepath.Join(vibekbRoot, filepath.FromSlash(rel))) {
			missingFiles = append(missingFiles, rel)
		}
	}
	return missingDirs, missingFiles, nil
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
	c.line("       prompts/INTEGRATE_VIBEKB.md")
	c.line("  3. When it finishes (PHP 8.2+ needed):  php tools/vibekb.php check")
	c.line("  4. Optional static site:  php tools/vibekb.php generate   (writes /docs)")
	c.blank()
	c.line("Preview the guide locally (needs PHP):  php -S localhost:8080 -t " + name)
	c.line("Then open:  http://localhost:8080/guide/")
	c.line("Repair the workspace any time:  php tools/vibekb.php bootstrap")
}

// ---- embedded-FS + filesystem helpers --------------------------------------

// embedEntries returns the file paths under an embedded payload path (a single
// file yields itself). Paths are repository-root-relative with forward slashes.
func embedEntries(p string) ([]string, error) {
	info, err := fs.Stat(vibekb.PayloadFS, p)
	if err != nil {
		return nil, err
	}
	if !info.IsDir() {
		return []string{p}, nil
	}
	var out []string
	err = fs.WalkDir(vibekb.PayloadFS, p, func(path string, d fs.DirEntry, err error) error {
		if err != nil {
			return err
		}
		if !d.IsDir() {
			out = append(out, path)
		}
		return nil
	})
	sort.Strings(out)
	return out, err
}

// readEmbedded returns the bytes of a file in the embedded payload.
func readEmbedded(embedPath string) ([]byte, error) {
	return vibekb.PayloadFS.ReadFile(embedPath)
}

func writeEmbedded(embedPath, dst string) error {
	b, err := readEmbedded(embedPath)
	if err != nil {
		return err
	}
	if err := os.MkdirAll(filepath.Dir(dst), 0o755); err != nil {
		return err
	}
	return os.WriteFile(dst, b, 0o644)
}

func fileExists(p string) bool {
	info, err := os.Stat(p)
	return err == nil && !info.IsDir()
}

func isDir(p string) bool {
	info, err := os.Stat(p)
	return err == nil && info.IsDir()
}

func projectName(target string) string {
	name := filepath.Base(strings.TrimRight(target, `/\`))
	if name == "" || name == "." || name == string(filepath.Separator) {
		return "this repository"
	}
	return name
}

// isSelfHostedRepo reports whether target holds VibeKB's own self-hosted model,
// which must never be reset by a fresh install.
func isSelfHostedRepo(target string) bool {
	b, err := os.ReadFile(filepath.Join(target, ".vibekb", "manifest.json"))
	if err != nil {
		return false
	}
	var m struct {
		SelfHosted bool `json:"self_hosted"`
	}
	if json.Unmarshal(b, &m) != nil {
		return false
	}
	return m.SelfHosted
}

func repoSignals(target string) []string {
	var signals []string
	if isDir(filepath.Join(target, ".git")) {
		signals = append(signals, "git repository")
	}
	for _, d := range []string{"src", "lib", "app", "source", "packages", "cmd", "internal"} {
		if isDir(filepath.Join(target, d)) {
			signals = append(signals, d+"/")
		}
	}
	for _, r := range []string{"README.md", "README", "README.rst", "README.txt"} {
		if fileExists(filepath.Join(target, r)) {
			signals = append(signals, r)
			break
		}
	}
	for _, mf := range []string{"package.json", "composer.json", "pyproject.toml", "go.mod", "Cargo.toml", "Gemfile", "pom.xml"} {
		if fileExists(filepath.Join(target, mf)) {
			signals = append(signals, mf)
			break
		}
	}
	return signals
}

func sortedKeys(m map[string]int) []string {
	out := make([]string, 0, len(m))
	for k := range m {
		out = append(out, k)
	}
	sort.Strings(out)
	return out
}
