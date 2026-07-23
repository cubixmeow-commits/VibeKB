package installer

import (
	"encoding/json"
	"testing"
)

// The embedded payload must contain a well-formed manifest with a non-empty
// payload — otherwise `vibekb install` cannot work.
func TestLoadManifest(t *testing.T) {
	m, err := loadManifest()
	if err != nil {
		t.Fatalf("loadManifest: %v", err)
	}
	if len(m.payloadPaths()) == 0 {
		t.Fatal("manifest payload is empty")
	}
	if m.starterDef() == "" {
		t.Fatal("starter definition path is empty")
	}
}

// The embedded starter definition must expose directories and files, and the
// canonical starter manifest must exist.
func TestEmbeddedStarter(t *testing.T) {
	m, err := loadManifest()
	if err != nil {
		t.Fatalf("loadManifest: %v", err)
	}
	dirs, err := starterDirs(m.starterDef())
	if err != nil {
		t.Fatalf("starterDirs: %v", err)
	}
	if len(dirs) == 0 {
		t.Fatal("starter defines no directories")
	}
	files, err := embedEntries(m.starterDef() + "/files")
	if err != nil {
		t.Fatalf("embedEntries: %v", err)
	}
	if len(files) == 0 {
		t.Fatal("starter defines no files")
	}
}

// Token substitution must yield valid JSON for the starter manifest.
func TestSubstituteTokensProducesValidJSON(t *testing.T) {
	m, err := loadManifest()
	if err != nil {
		t.Fatalf("loadManifest: %v", err)
	}
	b, err := readEmbedded(m.starterDef() + "/files/manifest.json")
	if err != nil {
		t.Fatalf("read starter manifest: %v", err)
	}
	out := substituteTokens(b, "2026-07-23", jsonString("my-app"))
	var v any
	if err := json.Unmarshal(out, &v); err != nil {
		t.Fatalf("substituted starter manifest is not valid JSON: %v", err)
	}
}

func TestJSONString(t *testing.T) {
	cases := map[string]string{
		"my-app": `"my-app"`,
		"a/b":    `"a/b"`, // slashes not escaped, matching PHP JSON_UNESCAPED_SLASHES
		`a"b`:    `"a\"b"`,
		"café":   `"café"`,
	}
	for in, want := range cases {
		if got := jsonString(in); got != want {
			t.Errorf("jsonString(%q) = %q, want %q", in, got, want)
		}
	}
}
