package installer

import (
	"fmt"
	"os"
	"path/filepath"
	"strings"
)

// Severity classifies a doctor finding.
type Severity string

const (
	SevError Severity = "error"
	SevWarn  Severity = "warning"
	SevInfo  Severity = "info"
	SevOK    Severity = "ok"
)

// Finding is one repository-footprint diagnostic.
type Finding struct {
	Severity Severity
	Label    string
	Detail   string
	Fix      string // suggested remedy, if any
}

// DiagnoseRepo inspects a repository for VibeKB footprint problems: legacy
// root-level installs, malformed/duplicate managed blocks, missing authoritative
// files, manifest drift, and unsafe collisions. It performs no repairs.
func DiagnoseRepo(root string) []Finding {
	var f []Finding
	m, err := loadManifest()
	if err != nil {
		return []Finding{{SevError, "installer payload", "embedded manifest is unreadable: " + err.Error(), ""}}
	}

	vibekbDir := filepath.Join(root, ".vibekb")
	if !isDir(vibekbDir) {
		return []Finding{{SevInfo, ".vibekb/", "no VibeKB workspace here", "run `vibekb install .` to create one"}}
	}

	// Foreign / unrecognized .vibekb.
	if !recognizedVibekb(root) {
		f = append(f, Finding{SevError, ".vibekb/", "a .vibekb/ exists but has no VibeKB manifest — unrecognized (possible collision)",
			"if it is not VibeKB's, move it aside; otherwise re-install"})
		return f
	}

	// Legacy root-level install.
	if legacyRootInstall(root) {
		f = append(f, Finding{SevWarn, "legacy layout", "root-level VibeKB files detected (pre-2.0 install)",
			"run `vibekb migrate .` to consolidate under .vibekb/"})
	}
	for _, name := range []string{"PRODUCT.md", "SCHEMA.md", "INSTALLER.md", "MAINTENANCE.md", "INITIALIZE.md"} {
		if fileExists(filepath.Join(root, name)) {
			f = append(f, Finding{SevWarn, "root doc", name + " at repository root (VibeKB now keeps it under .vibekb/reference/)",
				"run `vibekb migrate .`"})
		}
	}

	// Authoritative files.
	for _, ck := range []struct{ rel, label string }{
		{".vibekb/manifest.json", "model manifest"},
		{".vibekb/runtime/tools/vibekb.php", "runtime CLI"},
		{".vibekb/runtime/guide/index.php", "runtime guide"},
		{".vibekb/reference/WORKFLOW.md", "operating rules"},
		{".vibekb/prompts/INTEGRATE_VIBEKB.md", "integration prompt"},
	} {
		if !fileExists(filepath.Join(root, filepath.FromSlash(ck.rel))) {
			f = append(f, Finding{SevError, "missing", ck.label + " (" + ck.rel + ") is missing",
				"run `vibekb install .` to restore the runtime, or `vibekb bootstrap` for the model"})
		}
	}

	// Installation manifest + per-file drift.
	im := readInstallManifest(root, m.manifestPath())
	if im == nil {
		f = append(f, Finding{SevWarn, "install manifest", ".vibekb/install.json missing — ownership/hashes cannot be verified",
			"run `vibekb install .` (or `vibekb migrate .`) to write it"})
	} else {
		f = append(f, diagnoseManifest(root, im)...)
	}

	if len(f) == 0 {
		f = append(f, Finding{SevOK, "footprint", "VibeKB owns only .vibekb/ and its declared adapters; no problems found", ""})
	}
	return f
}

func diagnoseManifest(root string, im *installManifest) []Finding {
	var f []Finding
	for _, rec := range im.Files {
		abs := filepath.Join(root, filepath.FromSlash(rec.Path))
		switch rec.Kind {
		case "managed-block":
			b, err := os.ReadFile(abs)
			if err != nil {
				f = append(f, Finding{SevWarn, "adapter", rec.Path + " recorded but missing",
					"re-run `vibekb install .` to re-integrate, or ignore if intentionally removed"})
				continue
			}
			starts, ends := markerCounts(string(b))
			switch {
			case starts == 0 && ends == 0:
				f = append(f, Finding{SevInfo, "adapter", rec.Path + " no longer contains a VibeKB block", "re-run install to re-add it if wanted"})
			case starts != 1 || ends != 1:
				f = append(f, Finding{SevError, "managed block", fmt.Sprintf("%s has malformed/duplicate markers (%d START, %d END)", rec.Path, starts, ends),
					"fix the markers by hand so exactly one START/END pair remains, then re-run install"})
			default:
				if blk := extractBlock(b); blockHash(blk) != rec.BlockHash && rec.BlockHash != "" {
					f = append(f, Finding{SevInfo, "managed block", rec.Path + " block was edited since install", "run `vibekb install .` to restore the managed content"})
				}
			}
		case "payload", "namespaced":
			b, err := os.ReadFile(abs)
			if err != nil {
				f = append(f, Finding{SevWarn, "missing file", rec.Path + " recorded but missing", "run `vibekb install .` to restore it"})
				continue
			}
			if rec.InstalledHash != "" && sha256Hex(b) != rec.InstalledHash {
				sev := SevInfo
				lbl := "modified"
				if strings.HasPrefix(rec.Path, ".vibekb/runtime/") {
					lbl = "runtime modified"
				}
				f = append(f, Finding{sev, lbl, rec.Path + " differs from the installed version", "run `vibekb install --upgrade .` to refresh VibeKB-owned files"})
			}
		}
	}
	return f
}
