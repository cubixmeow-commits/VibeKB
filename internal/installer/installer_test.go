package installer

import (
	"encoding/json"
	"testing"
)

// The embedded payload must contain a well-formed manifest with a non-empty
// payload map — otherwise `vibekb install` cannot work.
func TestLoadManifest(t *testing.T) {
	m, err := loadManifest()
	if err != nil {
		t.Fatalf("loadManifest: %v", err)
	}
	if len(m.Payload.Map) == 0 {
		t.Fatal("manifest payload map is empty")
	}
	if m.starterDefSrc() == "" {
		t.Fatal("starter definition source path is empty")
	}
	if m.home() != ".vibekb" {
		t.Fatalf("home = %q, want .vibekb", m.home())
	}
	// Every payload destination must live under .vibekb/ — nothing at repo root.
	for _, e := range m.Payload.Map {
		if e.Dest == "" || e.Dest[:len(".vibekb")] != ".vibekb" {
			t.Fatalf("payload dest %q does not start under .vibekb/", e.Dest)
		}
	}
}

// The embedded starter definition must expose directories and files.
func TestEmbeddedStarter(t *testing.T) {
	m, err := loadManifest()
	if err != nil {
		t.Fatalf("loadManifest: %v", err)
	}
	dirs, err := starterDirs(m.starterDefSrc())
	if err != nil {
		t.Fatalf("starterDirs: %v", err)
	}
	if len(dirs) == 0 {
		t.Fatal("starter defines no directories")
	}
	files, err := embedEntries(m.starterDefSrc() + "/files")
	if err != nil {
		t.Fatalf("embedEntries: %v", err)
	}
	if len(files) == 0 {
		t.Fatal("starter defines no files")
	}
}

func TestSubstituteTokensProducesValidJSON(t *testing.T) {
	m, err := loadManifest()
	if err != nil {
		t.Fatalf("loadManifest: %v", err)
	}
	b, err := readEmbedded(m.starterDefSrc() + "/files/manifest.json")
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
		"a/b":    `"a/b"`,
		`a"b`:    `"a\"b"`,
		"café":   `"café"`,
	}
	for in, want := range cases {
		if got := jsonString(in); got != want {
			t.Errorf("jsonString(%q) = %q, want %q", in, got, want)
		}
	}
}
