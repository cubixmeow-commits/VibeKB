package installer

import (
	"bytes"
	"encoding/json"
	"io/fs"
	"os"
	"path/filepath"
	"sort"
	"strings"
	"time"

	vibekb "github.com/cubixmeow-commits/vibekb"
)

// ---- fresh .vibekb/ model scaffolding --------------------------------------

type scaffoldReport struct {
	createdDirs, createdFiles, kept, overwritten []string
	errors                                       []string
}

// scaffoldWorkspace creates (or repairs) a fresh, empty-but-valid model directly
// under <target>/.vibekb, from the embedded starter definition.
func scaffoldWorkspace(m manifest, target string, force bool) scaffoldReport {
	var rep scaffoldReport
	vibekbRoot := filepath.Join(target, filepath.FromSlash(m.home()))
	def := m.starterDefSrc()

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

func readEmbedded(embedPath string) ([]byte, error) {
	return vibekb.PayloadFS.ReadFile(embedPath)
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
