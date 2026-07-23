package installer

import (
	"bytes"
	"os"
	"path/filepath"
	"sort"
	"strings"
)

// action is the per-file disposition in a plan.
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

// payloadOp is one VibeKB-owned file to write under .vibekb/.
type payloadOp struct {
	src         string // embedded source path
	dest        string // repo-relative destination (forward slashes)
	action      action
	preExisting bool
}

// adapterOp is one optional integration outside .vibekb/.
type adapterOp struct {
	name        string
	kind        string // "namespaced" | "managed-block"
	dest        string
	src         string // namespaced: embedded file
	blockSrc    string // managed-block: embedded body
	preExisting bool

	// namespaced
	action action

	// managed-block
	existing []byte
	outcome  blockOutcome
}

// plan is the full set of intended operations.
type plan struct {
	payload         []payloadOp
	adapters        []adapterOp
	workspacePreset bool // an existing .vibekb/ model is present and will be preserved
	isUpgrade       bool
	legacyDetected  bool
}

// recognizedVibekb reports whether an existing .vibekb/ was created by VibeKB
// (has a model manifest, a v2 install manifest, or a legacy installer state).
func recognizedVibekb(target string) bool {
	for _, f := range []string{".vibekb/manifest.json", ".vibekb/install.json", ".vibekb/.installer.json"} {
		if fileExists(filepath.Join(target, filepath.FromSlash(f))) {
			return true
		}
	}
	return false
}

// legacyRootInstall reports whether target carries a pre-2.0 root-level VibeKB
// install (runtime and/or docs at the repository root).
func legacyRootInstall(target string) bool {
	if fileExists(filepath.Join(target, ".vibekb", ".installer.json")) {
		return true
	}
	if fileExists(filepath.Join(target, "tools", "vibekb.php")) && isDir(filepath.Join(target, "guide", "lib")) {
		if b, err := os.ReadFile(filepath.Join(target, "tools", "vibekb.php")); err == nil && bytes.Contains(b, []byte("VibeKB")) {
			return true
		}
	}
	return false
}

// isUpgrade reports whether target already holds a VibeKB install we should
// refresh in place rather than create fresh.
func detectUpgrade(target string, forced bool) bool {
	return forced ||
		fileExists(filepath.Join(target, ".vibekb", "install.json")) ||
		fileExists(filepath.Join(target, ".vibekb", ".installer.json"))
}

// selectAdapters resolves which integration adapters to apply, honouring
// --integrate / --no-integrations / --knowledge-only and per-adapter defaults.
func selectAdapters(m manifest, target string, opts options) []string {
	if opts.knowledgeOnly || opts.noIntegrations {
		return nil
	}
	names := make([]string, 0, len(m.Integrations.Adapters))
	for name := range m.Integrations.Adapters {
		names = append(names, name)
	}
	sort.Strings(names)

	if opts.integrateSet {
		// Explicit selection: only the named adapters, and only if defined.
		want := map[string]bool{}
		for _, n := range opts.integrate {
			want[strings.TrimSpace(n)] = true
		}
		var out []string
		for _, n := range names {
			if want[n] {
				out = append(out, n)
			}
		}
		return out
	}

	// Default selection: include an adapter when its default condition holds.
	var out []string
	for _, n := range names {
		a := m.Integrations.Adapters[n]
		if adapterDefaultApplies(a, target) {
			out = append(out, n)
		}
	}
	return out
}

func adapterDefaultApplies(a adapter, target string) bool {
	switch a.DefaultWhen {
	case "always":
		return true
	case "never", "":
		return false
	case "exists":
		return fileExists(filepath.Join(target, filepath.FromSlash(a.Dest)))
	case "detected":
		for _, d := range a.Detect {
			if isDir(filepath.Join(target, filepath.FromSlash(d))) || fileExists(filepath.Join(target, filepath.FromSlash(d))) {
				return true
			}
		}
		return false
	default:
		return false
	}
}

// buildPlan computes every intended operation without touching the filesystem.
func buildPlan(m manifest, target string, opts options) (plan, error) {
	var p plan
	p.isUpgrade = detectUpgrade(target, opts.upgrade)
	p.legacyDetected = legacyRootInstall(target)
	p.workspacePreset = isDir(filepath.Join(target, filepath.FromSlash(m.home()))) && recognizedVibekb(target) && !opts.force

	// ---- payload (always; VibeKB-owned, consolidated under .vibekb/) --------
	for _, entry := range m.Payload.Map {
		files, err := embedEntries(entry.Src)
		if err != nil {
			return p, err
		}
		for _, srcFile := range files {
			rel := strings.TrimPrefix(srcFile, entry.Src)
			dest := entry.Dest + rel // rel starts with "/" for dir entries, "" for file entries
			dest = filepath.ToSlash(filepath.Clean(dest))
			exists := fileExists(filepath.Join(target, filepath.FromSlash(dest)))
			act := actionCreate
			if exists {
				act = actionReplace
			}
			p.payload = append(p.payload, payloadOp{src: srcFile, dest: dest, action: act, preExisting: exists})
		}
	}
	sort.Slice(p.payload, func(i, j int) bool { return p.payload[i].dest < p.payload[j].dest })

	// ---- integrations (opt-in adapters outside .vibekb/) -------------------
	if !opts.knowledgeOnly {
		for _, name := range selectAdapters(m, target, opts) {
			a := m.Integrations.Adapters[name]
			destAbs := filepath.Join(target, filepath.FromSlash(a.Dest))
			exists := fileExists(destAbs)
			op := adapterOp{name: name, kind: a.Type, dest: a.Dest, src: a.Src, blockSrc: a.BlockSrc, preExisting: exists}
			switch a.Type {
			case "namespaced":
				op.action = actionCreate
				if exists {
					op.action = actionReplace
				}
			case "managed-block":
				body, err := readEmbedded(a.BlockSrc)
				if err != nil {
					return p, err
				}
				var existing []byte
				if exists {
					existing, _ = os.ReadFile(destAbs)
				}
				op.existing = existing
				op.outcome = applyManagedBlock(existing, string(body), m.blockVersion())
			}
			p.adapters = append(p.adapters, op)
		}
	}
	return p, nil
}
