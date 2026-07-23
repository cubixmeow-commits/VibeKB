package installer

import (
	"bytes"
	"fmt"
	"os"
	"path/filepath"
	"strings"
)

// legacySignatures identify a root-level file that a pre-2.0 install copied
// verbatim from VibeKB, so migration can recognise VibeKB-owned content without a
// manifest. A match plus the absence of a managed block means the whole file was
// VibeKB's.
var legacySignatures = map[string][]string{
	"CLAUDE.md": {"Canonical operating rules for AI agents working on VibeKB", "php tools/vibekb.php status"},
	"AGENTS.md": {"Entry point for coding agents", "php tools/vibekb.php status"},
}

// Migrate consolidates a legacy root-level VibeKB install under .vibekb/. It
// installs the consolidated layout, converts recognised root-level VibeKB files
// into managed blocks or reference copies, and removes only files it can
// positively identify as unmodified VibeKB content — never anything ambiguous.
func Migrate(args []string) int {
	opts, err := parseMigrateArgs(args)
	if err != nil {
		fmt.Fprintln(os.Stderr, "vibekb migrate: "+err.Error())
		return 2
	}
	if opts.help {
		migrateUsage(os.Stdout)
		return 0
	}

	c := newConsole()
	c.banner()
	m, err := loadManifest()
	if err != nil {
		c.errf("Corrupt payload: %v", err)
		return 1
	}
	target, err := resolveTarget(opts.target)
	if err != nil {
		c.errf("Target directory does not exist: %s", opts.target)
		return 1
	}
	if isSelfHostedRepo(target) {
		c.errf("Refusing to migrate VibeKB's own self-hosted repository.")
		return 1
	}

	c.kv("Target repository", projectName(target)+"  ("+target+")")
	c.kv("Mode", map[bool]string{true: "DRY RUN (no changes)", false: "migrate"}[opts.dryRun])
	c.blank()

	if !legacyRootInstall(target) && recognizedVibekb(target) {
		c.ok("This repository is already on the consolidated .vibekb/ layout — nothing to migrate.")
		return 0
	}

	var migrated, preserved, skipped []string

	// ---- 1. convert legacy shared docs (CLAUDE.md/AGENTS.md) first ----------
	// Done before consolidation so a whole-file VibeKB pointer becomes a managed
	// block rather than having a second block appended below it.
	for name := range legacySignatures {
		abs := filepath.Join(target, name)
		b, err := os.ReadFile(abs)
		if err != nil {
			continue
		}
		if bytes.Contains(b, []byte(blockStartPrefix)) {
			continue // already managed
		}
		if !matchesLegacy(name, b) {
			preserved = append(preserved, name+" (not recognised as VibeKB-owned — left as-is; a managed block will be added)")
			continue
		}
		body, _ := readEmbedded("template/integrations/agents-block.md")
		block := renderBlock(string(body), m.blockVersion(), detectEOL(b))
		if opts.dryRun {
			migrated = append(migrated, name+" (whole-file VibeKB pointer → managed block)")
			continue
		}
		if _, err := backupShared(target, name); err != nil {
			c.warn("could not back up " + name + ": " + err.Error())
		}
		if err := writeFileAtomic(abs, []byte(block+detectEOL(b)), 0o644); err != nil {
			c.errf("failed to rewrite %s: %v", name, err)
		} else {
			migrated = append(migrated, name+" (converted to a managed block)")
		}
	}

	// ---- 2. install the consolidated layout --------------------------------
	if !opts.dryRun {
		installOpts := options{target: target, yes: true}
		plan, err := buildPlan(m, target, installOpts)
		if err != nil {
			c.errf("planning failed: %v", err)
			return 1
		}
		res, ok := applyPlan(c, m, target, plan)
		if !ok {
			return 1
		}
		// Create or repair the model without overwriting existing records.
		_ = scaffoldWorkspace(m, target, false)
		prior := readInstallManifest(target, m.manifestPath())
		if err := writeInstallManifest(target, m, res.files, prior); err != nil {
			c.warn("could not write install manifest: " + err.Error())
		}
		migrated = append(migrated, fmt.Sprintf(".vibekb/ consolidated layout installed (%d file(s))", res.copiedFiles))
	} else {
		migrated = append(migrated, ".vibekb/ consolidated layout would be installed")
	}

	// ---- 3. relocate legacy root reference docs ----------------------------
	for _, name := range []string{"PRODUCT.md", "SCHEMA.md", "INITIALIZE.md", "MAINTENANCE.md", "INSTALLER.md"} {
		abs := filepath.Join(target, name)
		b, err := os.ReadFile(abs)
		if err != nil {
			continue
		}
		if !embeddedFileMatches(name, b) {
			preserved = append(preserved, name+" (modified since install — left in place; the canonical copy is at .vibekb/reference/)")
			continue
		}
		if opts.dryRun {
			migrated = append(migrated, name+" (unmodified → removed; now at .vibekb/reference/)")
			continue
		}
		_, _ = backupShared(target, name)
		if os.Remove(abs) == nil {
			migrated = append(migrated, name+" (removed; canonical copy at .vibekb/reference/)")
		}
	}

	// ---- 4. remove unmodified legacy root runtime dirs ---------------------
	for _, d := range []struct{ rootRel, embedSrc string }{
		{"guide", "guide"},
		{"tools", "tools"},
		{"prompts", "prompts"},
		{"template/starter", "template/starter"},
	} {
		abs := filepath.Join(target, filepath.FromSlash(d.rootRel))
		if !isDir(abs) {
			continue
		}
		clean, err := cleanDirMatchesEmbedded(target, d.rootRel, d.embedSrc)
		if err != nil || !clean {
			preserved = append(preserved, d.rootRel+"/ (contains modified or unrecognised files — left in place)")
			continue
		}
		if opts.dryRun {
			migrated = append(migrated, d.rootRel+"/ (unmodified VibeKB runtime → removed; now under .vibekb/runtime/)")
			continue
		}
		if os.RemoveAll(abs) == nil {
			migrated = append(migrated, d.rootRel+"/ (removed; now under .vibekb/runtime/)")
		}
		// Tidy an emptied template/ parent.
		removeEmptyParents(target, filepath.ToSlash(filepath.Dir(d.rootRel)))
	}

	// ---- 5. drop the legacy installer state --------------------------------
	legacyState := filepath.Join(target, ".vibekb", ".installer.json")
	if fileExists(legacyState) {
		if opts.dryRun {
			migrated = append(migrated, ".vibekb/.installer.json (legacy state → replaced by install.json)")
		} else if os.Remove(legacyState) == nil {
			migrated = append(migrated, ".vibekb/.installer.json (removed; replaced by install.json)")
		}
	}

	// ---- report ------------------------------------------------------------
	c.section("Migration summary")
	for _, x := range migrated {
		c.ok(x)
	}
	for _, x := range preserved {
		c.line("  keep    " + x)
	}
	for _, x := range skipped {
		c.warn("skip    " + x)
	}
	c.blank()
	if opts.dryRun {
		c.ok("Dry run complete. No files were changed. Re-run without --dry-run to apply.")
	} else {
		c.ok("Migration complete. Backups (if any) are under .vibekb/backups/.")
		c.line("Verify with:  vibekb check   (or php .vibekb/runtime/tools/vibekb.php check)")
	}
	return 0
}

func matchesLegacy(name string, b []byte) bool {
	sigs, ok := legacySignatures[name]
	if !ok {
		return false
	}
	for _, s := range sigs {
		if !bytes.Contains(b, []byte(s)) {
			return false
		}
	}
	return true
}

// embeddedFileMatches reports whether b equals the embedded file at embedPath.
func embeddedFileMatches(embedPath string, b []byte) bool {
	e, err := readEmbedded(embedPath)
	if err != nil {
		return false
	}
	return bytes.Equal(e, b)
}

// cleanDirMatchesEmbedded reports whether every regular file under rootRel in the
// target is byte-identical to its embedded counterpart under embedSrc (and has
// one) — i.e. the directory is unmodified VibeKB content, safe to remove.
func cleanDirMatchesEmbedded(target, rootRel, embedSrc string) (bool, error) {
	base := filepath.Join(target, filepath.FromSlash(rootRel))
	clean := true
	err := filepath.Walk(base, func(path string, info os.FileInfo, err error) error {
		if err != nil {
			return err
		}
		if info.IsDir() {
			return nil
		}
		rel, err := filepath.Rel(base, path)
		if err != nil {
			return err
		}
		embedPath := embedSrc + "/" + filepath.ToSlash(rel)
		b, err := os.ReadFile(path)
		if err != nil {
			clean = false
			return filepath.SkipDir
		}
		if !embeddedFileMatches(embedPath, b) {
			clean = false
			return fmt.Errorf("stop")
		}
		return nil
	})
	if err != nil && err.Error() == "stop" {
		return false, nil
	}
	return clean, err
}

type migrateOptions struct {
	target       string
	dryRun, help bool
}

func parseMigrateArgs(args []string) (migrateOptions, error) {
	o := migrateOptions{}
	for _, a := range args {
		switch a {
		case "--dry-run":
			o.dryRun = true
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

func migrateUsage(w *os.File) {
	fmt.Fprint(w, `vibekb migrate — consolidate a legacy root-level VibeKB install under .vibekb/.

  vibekb migrate [options] [target]

Installs the consolidated .vibekb/ layout, converts a whole-file VibeKB
CLAUDE.md/AGENTS.md into a managed block, relocates the reference docs, and
removes only root-level files it can positively identify as unmodified VibeKB
content. Modified or unrecognised files are preserved and reported. Backups are
written under .vibekb/backups/.

Options:
  --dry-run   Show what would change; write nothing.
  --help, -h  This help.
`)
}
