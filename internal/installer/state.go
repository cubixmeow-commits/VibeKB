package installer

import (
	"crypto/sha256"
	"encoding/hex"
	"encoding/json"
	"os"
	"path/filepath"
	"sort"
	"time"

	"github.com/cubixmeow-commits/vibekb/internal/buildinfo"
)

const sourceRepository = "https://github.com/cubixmeow-commits/VibeKB"

// ownership classifies every path VibeKB records.
type ownership string

const (
	ownVibeKB ownership = "vibekb" // VibeKB-owned, safe to manage/remove
	ownShared ownership = "shared" // pre-existing/user-owned; only a managed block is ours
)

// fileRecord is one entry in the installation manifest. It carries enough
// evidence to support upgrades, doctor, migration, and uninstall without storing
// absolute paths.
type fileRecord struct {
	Path          string    `json:"path"`                     // repo-relative, forward slashes
	Ownership     ownership `json:"ownership"`                // vibekb | shared
	Kind          string    `json:"kind"`                     // payload | namespaced | managed-block
	Integration   string    `json:"integration,omitempty"`    // adapter name, if any
	InstalledHash string    `json:"installed_hash,omitempty"` // sha256 of the file (whole-file kinds)
	BlockHash     string    `json:"block_hash,omitempty"`     // sha256 of the managed block (managed-block kind)
	BlockVersion  int       `json:"block_version,omitempty"`  // managed-block format version
	PreExisting   bool      `json:"pre_existing"`             // file existed before VibeKB touched it
	WholeFile     bool      `json:"whole_file"`               // VibeKB created the entire file (vs. only a block)
}

// installManifest is written to .vibekb/install.json.
type installManifest struct {
	SchemaVersion    int          `json:"schema_version"`
	InstallerVersion string       `json:"installer_version"`
	TemplateVersion  string       `json:"template_version"`
	SourceRepository string       `json:"source_repository"`
	InstalledAt      string       `json:"installed_at"`
	UpdatedAt        string       `json:"updated_at"`
	Files            []fileRecord `json:"files"`
	Note             string       `json:"note"`
}

func sha256Hex(b []byte) string {
	sum := sha256.Sum256(b)
	return "sha256:" + hex.EncodeToString(sum[:])
}

// readInstallManifest loads .vibekb/install.json, or nil if absent/unreadable.
func readInstallManifest(target, path string) *installManifest {
	b, err := os.ReadFile(filepath.Join(target, filepath.FromSlash(path)))
	if err != nil {
		return nil
	}
	var im installManifest
	if json.Unmarshal(b, &im) != nil {
		return nil
	}
	return &im
}

// writeInstallManifest writes the installation manifest, preserving the original
// installed_at from any prior manifest.
func writeInstallManifest(target string, m manifest, files []fileRecord, prior *installManifest) error {
	now := time.Now().UTC().Format(time.RFC3339)
	installedAt := now
	if prior != nil && prior.InstalledAt != "" {
		installedAt = prior.InstalledAt
	}
	sort.Slice(files, func(i, j int) bool { return files[i].Path < files[j].Path })
	im := installManifest{
		SchemaVersion:    2,
		InstallerVersion: buildinfo.Version,
		TemplateVersion:  m.TemplateVersion,
		SourceRepository: sourceRepository,
		InstalledAt:      installedAt,
		UpdatedAt:        now,
		Files:            files,
		Note:             "Written by the native vibekb installer. Records which paths VibeKB owns, which shared files it edited via a managed block, and hashes so upgrades, doctor, migration, and uninstall stay safe. No absolute paths. Do not edit by hand.",
	}
	rel := m.manifestPath()
	dst := filepath.Join(target, filepath.FromSlash(rel))
	if err := os.MkdirAll(filepath.Dir(dst), 0o755); err != nil {
		return err
	}
	b, err := json.MarshalIndent(im, "", "  ")
	if err != nil {
		return err
	}
	return os.WriteFile(dst, append(b, '\n'), 0o644)
}
