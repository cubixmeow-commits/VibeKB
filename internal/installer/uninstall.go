package installer

import (
	"fmt"
	"os"
	"path/filepath"
	"strings"
	"time"
)

// Uninstall removes VibeKB from a repository, ownership-aware: VibeKB-owned files
// (everything under .vibekb/, plus namespaced adapters) are removed, and shared
// files have only their managed block stripped — everything else is preserved.
func Uninstall(args []string) int {
	opts, err := parseUninstallArgs(args)
	if err != nil {
		fmt.Fprintln(os.Stderr, "vibekb uninstall: "+err.Error())
		return 2
	}
	if opts.help {
		uninstallUsage(os.Stdout)
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
		c.errf("Refusing to uninstall from VibeKB's own self-hosted repository.")
		return 1
	}

	c.kv("Target repository", projectName(target)+"  ("+target+")")
	c.kv("Mode", map[bool]string{true: "DRY RUN (no changes)", false: "uninstall"}[opts.dryRun])
	if opts.keepKnowledge {
		c.line("  Keeping the knowledge base (.vibekb/ model records) — removing only runtime and adapters.")
	}
	c.blank()

	im := readInstallManifest(target, m.manifestPath())
	if im == nil {
		c.warn("No installation manifest (.vibekb/install.json) found.")
		c.line("Without it, VibeKB can only remove the .vibekb/ directory and cannot")
		c.line("safely strip managed blocks from shared files. Nothing was changed.")
		c.line("If this is a legacy root-level install, run `vibekb migrate .` first.")
		return 1
	}

	var removed, kept, skipped []string
	var preservedBackups string

	// ---- shared files: strip only the managed block ------------------------
	for _, rec := range im.Files {
		if rec.Kind != "managed-block" {
			continue
		}
		abs := filepath.Join(target, filepath.FromSlash(rec.Path))
		existing, err := os.ReadFile(abs)
		if err != nil {
			continue // already gone
		}
		out := removeManagedBlock(existing)
		switch out.Action {
		case blockConflict:
			skipped = append(skipped, rec.Path+" (malformed markers — left untouched)")
			continue
		case blockNoop:
			continue
		}
		// If VibeKB created the whole file solely for the block, remove it.
		fileIsOnlyOurs := !rec.PreExisting && len(strings.TrimSpace(string(out.Content))) == 0
		if opts.dryRun {
			if fileIsOnlyOurs {
				removed = append(removed, rec.Path+" (created by VibeKB — would delete)")
			} else {
				removed = append(removed, rec.Path+" (would strip managed block, preserve the rest)")
			}
			continue
		}
		if _, err := backupShared(target, rec.Path); err != nil {
			c.warn("could not back up " + rec.Path + ": " + err.Error())
		}
		if fileIsOnlyOurs {
			_ = os.Remove(abs)
			removed = append(removed, rec.Path+" (was VibeKB-only)")
		} else {
			if err := writeFileAtomic(abs, out.Content, 0o644); err != nil {
				c.errf("failed to update %s: %v", rec.Path, err)
			} else {
				removed = append(removed, rec.Path+" (managed block stripped)")
			}
		}
	}

	// ---- namespaced adapters: remove the VibeKB-owned file -----------------
	for _, rec := range im.Files {
		if rec.Kind != "namespaced" {
			continue
		}
		abs := filepath.Join(target, filepath.FromSlash(rec.Path))
		if !fileExists(abs) {
			continue
		}
		if rec.PreExisting {
			skipped = append(skipped, rec.Path+" (existed before VibeKB — left in place)")
			continue
		}
		if opts.dryRun {
			removed = append(removed, rec.Path+" (namespaced adapter)")
			continue
		}
		_ = os.Remove(abs)
		removeEmptyParents(target, filepath.Dir(rec.Path))
		removed = append(removed, rec.Path)
	}

	// ---- .vibekb/ itself ---------------------------------------------------
	vibekbRoot := filepath.Join(target, ".vibekb")
	backupsDir := filepath.Join(vibekbRoot, "backups")
	if opts.keepKnowledge {
		// Retain model records and any shared-file backups; remove only runtime
		// payload and the install manifest.
		for _, sub := range []string{"runtime", "reference", "prompts", "generated", "install.json"} {
			p := filepath.Join(vibekbRoot, sub)
			if !pathExists(p) {
				continue
			}
			if opts.dryRun {
				removed = append(removed, ".vibekb/"+sub)
				continue
			}
			_ = os.RemoveAll(p)
			removed = append(removed, ".vibekb/"+sub)
		}
		kept = append(kept, ".vibekb/ (model records retained via --keep-knowledge)")
		if pathExists(backupsDir) {
			kept = append(kept, ".vibekb/backups/ (shared-file backups retained)")
		}
	} else {
		if pathExists(vibekbRoot) {
			if opts.dryRun {
				removed = append(removed, ".vibekb/ (entire directory)")
				if dirHasEntries(backupsDir) {
					kept = append(kept, ".vibekb/backups/ would be relocated so shared-file backups survive")
				}
			} else {
				// Relocate backups out of .vibekb/ before deletion so install-
				// and uninstall-time shared-file snapshots are not lost.
				if dirHasEntries(backupsDir) {
					stamp := time.Now().UTC().Format("20060102T150405.000Z")
					dest := filepath.Join(os.TempDir(), "vibekb-backups-"+projectName(target)+"-"+stamp+"-"+fmt.Sprintf("%d", os.Getpid()))
					// Ensure uniqueness even when two uninstalls land in the same millisecond.
					for n := 0; pathExists(dest); n++ {
						dest = filepath.Join(os.TempDir(), "vibekb-backups-"+projectName(target)+"-"+stamp+"-"+fmt.Sprintf("%d-%d", os.Getpid(), n))
					}
					if err := os.Rename(backupsDir, dest); err != nil {
						// Cross-device rename can fail; fall back to copy+remove.
						if copyErr := copyDir(backupsDir, dest); copyErr != nil {
							c.warn("could not preserve .vibekb/backups/: " + copyErr.Error() + " (rename: " + err.Error() + ")")
						} else {
							_ = os.RemoveAll(backupsDir)
							preservedBackups = dest
						}
					} else {
						preservedBackups = dest
					}
				}
				_ = os.RemoveAll(vibekbRoot)
				removed = append(removed, ".vibekb/ (entire directory)")
			}
		}
	}

	// ---- report ------------------------------------------------------------
	c.section("Summary")
	for _, r := range removed {
		c.ok("remove  " + r)
	}
	for _, k := range kept {
		c.line("  keep    " + k)
	}
	for _, s := range skipped {
		c.warn("skip    " + s)
	}
	if preservedBackups != "" {
		c.line("  backups " + preservedBackups)
		kept = append(kept, "shared-file backups preserved outside .vibekb/")
	}
	c.blank()
	if opts.dryRun {
		c.ok("Dry run complete. No files were changed.")
	} else {
		c.ok("VibeKB uninstalled. Content outside VibeKB's ownership was preserved.")
		if preservedBackups != "" {
			c.line("Shared-file backups were moved to: " + preservedBackups)
		}
	}
	return 0
}

type uninstallOptions struct {
	target                           string
	dryRun, keepKnowledge, yes, help bool
}

func parseUninstallArgs(args []string) (uninstallOptions, error) {
	o := uninstallOptions{}
	for _, a := range args {
		switch a {
		case "--dry-run":
			o.dryRun = true
		case "--keep-knowledge":
			o.keepKnowledge = true
		case "--yes", "-y":
			o.yes = true
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

func uninstallUsage(w *os.File) {
	fmt.Fprint(w, `vibekb uninstall — remove VibeKB from a repository, safely.

  vibekb uninstall [options] [target]

Removes VibeKB-owned files (everything under .vibekb/ and namespaced adapters)
and strips only VibeKB's managed block from shared files (AGENTS.md, CLAUDE.md,
…), preserving everything else. Shared files that VibeKB created solely to hold
its block are removed; files that pre-existed VibeKB are left in place.
Shared-file backups under .vibekb/backups/ are relocated to a temp directory
so they survive a full uninstall (or retained under .vibekb/ with --keep-knowledge).

Options:
  --keep-knowledge  Keep the .vibekb/ model records; remove only runtime + adapters.
  --dry-run         Show what would be removed; change nothing.
  --yes, -y         Assume "yes" to prompts (non-interactive).
  --help, -h        This help.
`)
}

func pathExists(p string) bool {
	_, err := os.Stat(p)
	return err == nil
}

func dirHasEntries(p string) bool {
	entries, err := os.ReadDir(p)
	return err == nil && len(entries) > 0
}

// copyDir recursively copies src to dst (used when os.Rename cannot cross devices).
func copyDir(src, dst string) error {
	if err := os.MkdirAll(dst, 0o755); err != nil {
		return err
	}
	return filepath.Walk(src, func(path string, info os.FileInfo, err error) error {
		if err != nil {
			return err
		}
		rel, err := filepath.Rel(src, path)
		if err != nil {
			return err
		}
		out := filepath.Join(dst, rel)
		if info.IsDir() {
			return os.MkdirAll(out, 0o755)
		}
		b, err := os.ReadFile(path)
		if err != nil {
			return err
		}
		return writeFileAtomic(out, b, info.Mode())
	})
}

// removeEmptyParents removes now-empty parent directories up to (not including)
// the target root — e.g. .cursor/rules after its last rule is gone.
func removeEmptyParents(target, relDir string) {
	for relDir != "." && relDir != "" && relDir != "/" {
		abs := filepath.Join(target, filepath.FromSlash(relDir))
		entries, err := os.ReadDir(abs)
		if err != nil || len(entries) > 0 {
			return
		}
		if os.Remove(abs) != nil {
			return
		}
		relDir = filepath.ToSlash(filepath.Dir(relDir))
	}
}
