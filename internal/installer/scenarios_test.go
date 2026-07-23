package installer

import (
	"os"
	"os/exec"
	"path/filepath"
	"strings"
	"testing"
)

// newConsole reads from os.Stdin; in tests we always pass yes/non-interactive
// options so confirm() is never reached.

func tmpRepo(t *testing.T) string {
	t.Helper()
	dir := t.TempDir()
	// Make it look like a project so the sanity check passes non-interactively.
	if err := os.MkdirAll(filepath.Join(dir, ".git"), 0o755); err != nil {
		t.Fatal(err)
	}
	if err := os.WriteFile(filepath.Join(dir, "README.md"), []byte("# demo\n"), 0o644); err != nil {
		t.Fatal(err)
	}
	return dir
}

func exists(t *testing.T, parts ...string) bool {
	t.Helper()
	_, err := os.Stat(filepath.Join(parts...))
	return err == nil
}

func read(t *testing.T, parts ...string) string {
	t.Helper()
	b, err := os.ReadFile(filepath.Join(parts...))
	if err != nil {
		t.Fatalf("read %v: %v", parts, err)
	}
	return string(b)
}

// installInto runs the installer non-interactively against dir.
func installInto(t *testing.T, dir string, extra ...string) int {
	t.Helper()
	args := append([]string{"--yes", dir}, extra...)
	return Run(args)
}

func TestInstallIntoEmptyRepo(t *testing.T) {
	dir := tmpRepo(t)
	if code := installInto(t, dir); code != 0 {
		t.Fatalf("install exit code %d", code)
	}
	// Everything VibeKB owns is under .vibekb/ — nothing new at repo root.
	for _, root := range []string{"CLAUDE.md", "AGENTS.md", "PRODUCT.md", "SCHEMA.md", "guide", "tools", "prompts", "template"} {
		if exists(t, dir, root) {
			t.Errorf("root-level %q was created — should live under .vibekb/", root)
		}
	}
	for _, want := range []string{
		".vibekb/manifest.json",
		".vibekb/install.json",
		".vibekb/runtime/guide/index.php",
		".vibekb/runtime/tools/vibekb.php",
		".vibekb/reference/WORKFLOW.md",
		".vibekb/prompts/INTEGRATE_VIBEKB.md",
	} {
		if !exists(t, dir, filepath.FromSlash(want)) {
			t.Errorf("expected %q to exist", want)
		}
	}
}

func TestInstallDefaultDoesNotCreateAgentsFiles(t *testing.T) {
	dir := tmpRepo(t)
	installInto(t, dir)
	if exists(t, dir, "AGENTS.md") || exists(t, dir, "CLAUDE.md") {
		t.Fatal("default install must not create AGENTS.md/CLAUDE.md when absent")
	}
}

func TestInstallWithExistingAgentsInsertsBlockAndPreserves(t *testing.T) {
	dir := tmpRepo(t)
	orig := "# My agent rules\n\nDo the thing.\n"
	if err := os.WriteFile(filepath.Join(dir, "AGENTS.md"), []byte(orig), 0o644); err != nil {
		t.Fatal(err)
	}
	installInto(t, dir)
	got := read(t, dir, "AGENTS.md")
	if !strings.Contains(got, "# My agent rules") || !strings.Contains(got, "Do the thing.") {
		t.Fatalf("pre-existing content lost:\n%s", got)
	}
	if !strings.Contains(got, blockStartPrefix) {
		t.Fatalf("managed block not inserted:\n%s", got)
	}
}

func TestInstallWithExistingClaudePreservesOutsideBlock(t *testing.T) {
	dir := tmpRepo(t)
	orig := "# Claude rules\nline1\nline2\n"
	os.WriteFile(filepath.Join(dir, "CLAUDE.md"), []byte(orig), 0o644)
	installInto(t, dir)
	got := read(t, dir, "CLAUDE.md")
	if !strings.HasPrefix(got, orig) {
		t.Fatalf("content outside block changed:\n%s", got)
	}
}

func TestRepeatedInstallNoDuplicateBlocks(t *testing.T) {
	dir := tmpRepo(t)
	os.WriteFile(filepath.Join(dir, "AGENTS.md"), []byte("# rules\n"), 0o644)
	installInto(t, dir)
	installInto(t, dir) // upgrade
	got := read(t, dir, "AGENTS.md")
	if n := strings.Count(got, blockStartPrefix); n != 1 {
		t.Fatalf("expected exactly one managed block, found %d", n)
	}
}

func TestInstallCRLFAgentsPreservesLineEndings(t *testing.T) {
	dir := tmpRepo(t)
	os.WriteFile(filepath.Join(dir, "CLAUDE.md"), []byte("# rules\r\nwin\r\n"), 0o644)
	installInto(t, dir)
	got := read(t, dir, "CLAUDE.md")
	if strings.Contains(strings.ReplaceAll(got, "\r\n", ""), "\n") {
		t.Fatalf("LF leaked into CRLF file:\n%q", got)
	}
}

func TestKnowledgeOnlyTouchesNoIntegrations(t *testing.T) {
	dir := tmpRepo(t)
	os.WriteFile(filepath.Join(dir, "AGENTS.md"), []byte("# rules\n"), 0o644)
	os.MkdirAll(filepath.Join(dir, ".cursor"), 0o755)
	installInto(t, dir, "--knowledge-only")
	if strings.Contains(read(t, dir, "AGENTS.md"), blockStartPrefix) {
		t.Fatal("--knowledge-only must not edit AGENTS.md")
	}
	if exists(t, dir, ".cursor/rules/vibekb.mdc") {
		t.Fatal("--knowledge-only must not create the cursor adapter")
	}
	if !exists(t, dir, ".vibekb/manifest.json") {
		t.Fatal("--knowledge-only must still install the .vibekb/ model")
	}
}

func TestIntegrateSelectsNamedAdapterOnly(t *testing.T) {
	dir := tmpRepo(t)
	os.WriteFile(filepath.Join(dir, "AGENTS.md"), []byte("# rules\n"), 0o644)
	installInto(t, dir, "--integrate", "cursor")
	if !exists(t, dir, ".cursor/rules/vibekb.mdc") {
		t.Fatal("--integrate cursor should create the cursor adapter even when absent")
	}
	if strings.Contains(read(t, dir, "AGENTS.md"), blockStartPrefix) {
		t.Fatal("--integrate cursor must not touch AGENTS.md")
	}
}

func TestDryRunWritesNothing(t *testing.T) {
	dir := tmpRepo(t)
	if code := Run([]string{"--yes", "--dry-run", dir}); code != 0 {
		t.Fatalf("dry-run exit %d", code)
	}
	if exists(t, dir, ".vibekb") {
		t.Fatal("--dry-run created files")
	}
}

func TestForeignVibekbIsCollision(t *testing.T) {
	dir := tmpRepo(t)
	// A .vibekb/ we did not create (no manifest/install/installer markers).
	os.MkdirAll(filepath.Join(dir, ".vibekb"), 0o755)
	os.WriteFile(filepath.Join(dir, ".vibekb", "notes.txt"), []byte("mine\n"), 0o644)
	if code := installInto(t, dir); code == 0 {
		t.Fatal("expected non-zero exit for an unrecognized .vibekb/")
	}
	if exists(t, dir, ".vibekb/manifest.json") {
		t.Fatal("installer wrote into a foreign .vibekb/ without --force")
	}
}

func TestUpgradePreservesModelEdits(t *testing.T) {
	dir := tmpRepo(t)
	installInto(t, dir)
	// Simulate a user edit to a model file.
	intent := filepath.Join(dir, ".vibekb", "project", "intent.md")
	os.WriteFile(intent, []byte("# EDITED intent\n"), 0o644)
	installInto(t, dir) // upgrade
	if got := read(t, dir, ".vibekb/project/intent.md"); !strings.Contains(got, "EDITED intent") {
		t.Fatalf("upgrade clobbered a user model edit:\n%s", got)
	}
	// But runtime is refreshed.
	if !exists(t, dir, ".vibekb/runtime/tools/vibekb.php") {
		t.Fatal("runtime missing after upgrade")
	}
}

func TestInstallManifestRecordsOwnership(t *testing.T) {
	dir := tmpRepo(t)
	os.WriteFile(filepath.Join(dir, "AGENTS.md"), []byte("# rules\n"), 0o644)
	installInto(t, dir)
	m, _ := loadManifest()
	im := readInstallManifest(dir, m.manifestPath())
	if im == nil {
		t.Fatal("no install manifest written")
	}
	var sawPayload, sawBlock bool
	for _, f := range im.Files {
		if f.Kind == "payload" && f.Ownership == ownVibeKB {
			sawPayload = true
		}
		if f.Kind == "managed-block" && f.Ownership == ownShared && f.Path == "AGENTS.md" {
			sawBlock = true
			if !f.PreExisting {
				t.Error("AGENTS.md should be recorded pre_existing")
			}
		}
	}
	if !sawPayload || !sawBlock {
		t.Fatalf("manifest missing records: payload=%v block=%v", sawPayload, sawBlock)
	}
}

func TestUninstallRemovesOwnedPreservesShared(t *testing.T) {
	dir := tmpRepo(t)
	orig := "# My rules\n\nKeep me.\n"
	os.WriteFile(filepath.Join(dir, "AGENTS.md"), []byte(orig), 0o644)
	os.MkdirAll(filepath.Join(dir, ".cursor"), 0o755)
	installInto(t, dir)
	if !exists(t, dir, ".cursor/rules/vibekb.mdc") {
		t.Fatal("precondition: cursor adapter should be installed (.cursor present)")
	}

	if code := Uninstall([]string{"--yes", dir}); code != 0 {
		t.Fatalf("uninstall exit %d", code)
	}
	if exists(t, dir, ".vibekb") {
		t.Fatal("uninstall left .vibekb/ behind")
	}
	if exists(t, dir, ".cursor/rules/vibekb.mdc") {
		t.Fatal("uninstall left the namespaced cursor adapter behind")
	}
	got := read(t, dir, "AGENTS.md")
	if !strings.Contains(got, "Keep me.") {
		t.Fatalf("uninstall lost user content:\n%s", got)
	}
	if strings.Contains(got, blockStartPrefix) {
		t.Fatalf("uninstall left the managed block:\n%s", got)
	}
}

func TestUninstallKeepKnowledge(t *testing.T) {
	dir := tmpRepo(t)
	installInto(t, dir)
	Uninstall([]string{"--yes", "--keep-knowledge", dir})
	if !exists(t, dir, ".vibekb/manifest.json") {
		t.Fatal("--keep-knowledge should retain the model")
	}
	if exists(t, dir, ".vibekb/runtime") {
		t.Fatal("--keep-knowledge should remove the runtime")
	}
}

func TestForceTakesOverForeignVibekb(t *testing.T) {
	dir := tmpRepo(t)
	os.MkdirAll(filepath.Join(dir, ".vibekb"), 0o755)
	os.WriteFile(filepath.Join(dir, ".vibekb", "notes.txt"), []byte("mine\n"), 0o644)
	if code := installInto(t, dir, "--force"); code != 0 {
		t.Fatalf("install --force exit %d", code)
	}
	if !exists(t, dir, ".vibekb/manifest.json") || !exists(t, dir, ".vibekb/install.json") {
		t.Fatal("--force should scaffold a recognised VibeKB model")
	}
	// Foreign content is not deleted; VibeKB only writes its own paths.
	if !exists(t, dir, ".vibekb/notes.txt") {
		t.Fatal("--force must not delete unrecognized files inside .vibekb/")
	}
	if got := read(t, dir, ".vibekb/notes.txt"); got != "mine\n" {
		t.Fatalf("foreign content altered: %q", got)
	}
}

func TestUninstallPreservesSharedFileBackups(t *testing.T) {
	dir := tmpRepo(t)
	orig := "# My rules\n\nKeep me.\n"
	os.WriteFile(filepath.Join(dir, "AGENTS.md"), []byte(orig), 0o644)
	installInto(t, dir)
	if !exists(t, dir, ".vibekb/backups") {
		t.Fatal("precondition: install should have backed up AGENTS.md")
	}
	before, _ := os.ReadDir(os.TempDir())
	beforeSet := map[string]bool{}
	for _, e := range before {
		beforeSet[e.Name()] = true
	}
	if code := Uninstall([]string{"--yes", dir}); code != 0 {
		t.Fatalf("uninstall exit %d", code)
	}
	if exists(t, dir, ".vibekb") {
		t.Fatal("uninstall left .vibekb/")
	}
	// Backups must survive outside the deleted .vibekb/ (relocated under temp).
	entries, _ := os.ReadDir(os.TempDir())
	found := false
	for _, e := range entries {
		name := e.Name()
		if beforeSet[name] || !strings.HasPrefix(name, "vibekb-backups-") || !e.IsDir() {
			continue
		}
		bakEntries, _ := os.ReadDir(filepath.Join(os.TempDir(), name))
		for _, b := range bakEntries {
			if strings.HasPrefix(b.Name(), "AGENTS.md.") && strings.HasSuffix(b.Name(), ".bak") {
				body, _ := os.ReadFile(filepath.Join(os.TempDir(), name, b.Name()))
				if strings.Contains(string(body), "Keep me.") {
					found = true
				}
			}
		}
	}
	if !found {
		t.Fatal("uninstall did not preserve shared-file backups outside .vibekb/")
	}
	got := read(t, dir, "AGENTS.md")
	if !strings.Contains(got, "Keep me.") || strings.Contains(got, blockStartPrefix) {
		t.Fatalf("shared content not restored cleanly:\n%s", got)
	}
}

func TestUninstallKeepKnowledgeRetainsBackups(t *testing.T) {
	dir := tmpRepo(t)
	os.WriteFile(filepath.Join(dir, "AGENTS.md"), []byte("# rules\n"), 0o644)
	installInto(t, dir)
	Uninstall([]string{"--yes", "--keep-knowledge", dir})
	if !exists(t, dir, ".vibekb/backups") {
		t.Fatal("--keep-knowledge must retain .vibekb/backups/")
	}
	if exists(t, dir, ".vibekb/runtime") {
		t.Fatal("--keep-knowledge should still remove runtime")
	}
}

func TestMigrateRejectsLookalikeUserClaude(t *testing.T) {
	dir := tmpRepo(t)
	// Quotes a VibeKB phrase but is not a whole-file pointer (wrong title line).
	lookalike := "# My Claude rules\n\nSee also Canonical operating rules for AI agents working on VibeKB\nand php tools/vibekb.php status and VibeKB is self-hosted.\n"
	os.WriteFile(filepath.Join(dir, "CLAUDE.md"), []byte(lookalike), 0o644)
	os.MkdirAll(filepath.Join(dir, ".vibekb"), 0o755)
	os.WriteFile(filepath.Join(dir, ".vibekb", ".installer.json"), []byte(`{}`), 0o644)
	Migrate([]string{dir})
	got := read(t, dir, "CLAUDE.md")
	if !strings.Contains(got, "# My Claude rules") {
		t.Fatalf("lookalike user CLAUDE.md was replaced:\n%s", got)
	}
	if strings.HasPrefix(strings.TrimLeft(got, "\r\n"), "<!-- VIBEKB:START") && !strings.Contains(got, "# My Claude rules") {
		t.Fatal("user title lost")
	}
}

func TestMigrateFromLegacyRootDocs(t *testing.T) {
	dir := tmpRepo(t)
	// A legacy whole-file VibeKB CLAUDE.md pointer (title + signatures).
	legacy := "# CLAUDE.md — Canonical operating rules for AI agents working on VibeKB\n\nVibeKB is self-hosted.\n\nRun php tools/vibekb.php status\n"
	os.WriteFile(filepath.Join(dir, "CLAUDE.md"), []byte(legacy), 0o644)
	// A legacy installer state to mark it as a prior install.
	os.MkdirAll(filepath.Join(dir, ".vibekb"), 0o755)
	os.WriteFile(filepath.Join(dir, ".vibekb", ".installer.json"), []byte(`{"template_version":"1.0.0"}`), 0o644)

	if code := Migrate([]string{dir}); code != 0 {
		t.Fatalf("migrate exit %d", code)
	}
	got := read(t, dir, "CLAUDE.md")
	if !strings.Contains(got, blockStartPrefix) {
		t.Fatalf("legacy CLAUDE.md not converted to a managed block:\n%s", got)
	}
	if strings.Contains(got, "Canonical operating rules") {
		t.Fatalf("legacy whole-file content should have been replaced by the block:\n%s", got)
	}
	if !exists(t, dir, ".vibekb/runtime/tools/vibekb.php") {
		t.Fatal("migrate did not install consolidated runtime")
	}
	if exists(t, dir, ".vibekb/.installer.json") {
		t.Fatal("migrate did not drop the legacy installer state")
	}
	if !exists(t, dir, ".vibekb/backups") {
		t.Fatal("migrate should have backed up the converted CLAUDE.md")
	}
}

func TestMigratePreservesUserModifiedRootDoc(t *testing.T) {
	dir := tmpRepo(t)
	os.WriteFile(filepath.Join(dir, "CLAUDE.md"), []byte("# totally my own file\nnot vibekb\n"), 0o644)
	os.MkdirAll(filepath.Join(dir, ".vibekb"), 0o755)
	os.WriteFile(filepath.Join(dir, ".vibekb", ".installer.json"), []byte(`{}`), 0o644)
	Migrate([]string{dir})
	got := read(t, dir, "CLAUDE.md")
	if !strings.Contains(got, "totally my own file") {
		t.Fatalf("user CLAUDE.md content lost:\n%s", got)
	}
	// It should have received a managed block (integration), not been replaced.
	if !strings.Contains(got, blockStartPrefix) {
		t.Fatalf("expected a managed block added to user file:\n%s", got)
	}
}

func TestConsolidatedGenerateCopiesAssets(t *testing.T) {
	php, err := exec.LookPath("php")
	if err != nil {
		t.Skip("php not available")
	}
	dir := tmpRepo(t)
	if code := installInto(t, dir); code != 0 {
		t.Fatalf("install exit %d", code)
	}
	gen := filepath.Join(dir, ".vibekb", "runtime", "tools", "generate-static.php")
	cmd := exec.Command(php, gen)
	cmd.Dir = dir
	out, err := cmd.CombinedOutput()
	if err != nil {
		t.Fatalf("generate-static failed: %v\n%s", err, out)
	}
	css := filepath.Join(dir, ".vibekb", "generated", "assets", "css", "guide.css")
	js := filepath.Join(dir, ".vibekb", "generated", "assets", "js", "guide.js")
	if !fileExists(css) {
		t.Fatalf("consolidated generate missing CSS at %s\n%s", css, out)
	}
	if !fileExists(js) {
		t.Fatalf("consolidated generate missing JS at %s\n%s", js, out)
	}
	if !exists(t, dir, ".vibekb/generated/index.html") {
		t.Fatal("consolidated generate did not write index.html under .vibekb/generated/")
	}
	// Must never write into a repository-owned root docs/ directory.
	if exists(t, dir, "docs") {
		t.Fatal("consolidated generate wrote root docs/ — should use .vibekb/generated/")
	}
}
