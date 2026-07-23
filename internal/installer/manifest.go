package installer

import (
	"encoding/json"
	"fmt"

	vibekb "github.com/cubixmeow-commits/vibekb"
)

// manifest is the parsed template/manifest.json (schema 2) — the single source
// of truth for what a VibeKB installation places in a target repository.
type manifest struct {
	TemplateVersion string `json:"template_version"`
	SchemaVersion   int    `json:"schema_version"`
	Home            string `json:"home"`

	Payload struct {
		Map []mapEntry `json:"map"`
	} `json:"payload"`

	Integrations struct {
		Adapters map[string]adapter `json:"adapters"`
	} `json:"integrations"`

	BlockMarkers struct {
		Version int `json:"version"`
	} `json:"block_markers"`

	ManifestFile struct {
		Path string `json:"path"`
	} `json:"manifest_file"`

	StarterModel struct {
		Root                string `json:"root"`
		DefinitionSrc       string `json:"definition_src"`
		DefinitionInstalled string `json:"definition_installed"`
	} `json:"starter_model"`

	Legacy struct {
		RootDocs  []string `json:"root_docs"`
		RootDirs  []string `json:"root_dirs"`
		StateFile string   `json:"state_file"`
	} `json:"legacy"`
}

// mapEntry maps an embedded source path to its destination in a target repo.
type mapEntry struct {
	Src  string `json:"src"`
	Dest string `json:"dest"`
}

// adapter is one optional integration outside .vibekb/.
type adapter struct {
	Type        string   `json:"type"`         // "namespaced" | "managed-block"
	Src         string   `json:"src"`          // namespaced: embedded source file
	Dest        string   `json:"dest"`         // where it is written
	BlockSrc    string   `json:"block_src"`    // managed-block: embedded body
	Detect      []string `json:"detect"`       // namespaced: dirs whose presence triggers default install
	DefaultWhen string   `json:"default_when"` // "detected" | "exists" | "always" | "never"
}

func (m manifest) home() string {
	if m.Home != "" {
		return m.Home
	}
	return ".vibekb"
}

func (m manifest) starterDefInstalled() string {
	if m.StarterModel.DefinitionInstalled != "" {
		return m.StarterModel.DefinitionInstalled
	}
	return ".vibekb/runtime/template/starter"
}

// starterDefSrc is the embedded (repo-root-relative) starter definition path.
func (m manifest) starterDefSrc() string {
	if m.StarterModel.DefinitionSrc != "" {
		return m.StarterModel.DefinitionSrc
	}
	return "template/starter"
}

func (m manifest) blockVersion() int {
	if m.BlockMarkers.Version > 0 {
		return m.BlockMarkers.Version
	}
	return 1
}

func (m manifest) manifestPath() string {
	if m.ManifestFile.Path != "" {
		return m.ManifestFile.Path
	}
	return ".vibekb/install.json"
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
	if len(m.Payload.Map) == 0 {
		return m, fmt.Errorf("manifest declares no payload map")
	}
	if m.SchemaVersion != 2 {
		return m, fmt.Errorf("unsupported manifest schema_version %d (expected 2)", m.SchemaVersion)
	}
	return m, nil
}
