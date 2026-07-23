package installer

import (
	"io/fs"
	"os"
	"path/filepath"
	"strings"
	"time"
)

// writeFileAtomic writes b to dst via a temporary file + rename, so a reader
// never observes a partially written file. Parent directories are created.
func writeFileAtomic(dst string, b []byte, perm fs.FileMode) error {
	if err := os.MkdirAll(filepath.Dir(dst), 0o755); err != nil {
		return err
	}
	tmp := dst + ".vibekb-tmp"
	if err := os.WriteFile(tmp, b, perm); err != nil {
		return err
	}
	if err := os.Rename(tmp, dst); err != nil {
		_ = os.Remove(tmp)
		return err
	}
	return nil
}

func writeEmbeddedAtomic(embedPath, dst string) ([]byte, error) {
	b, err := readEmbedded(embedPath)
	if err != nil {
		return nil, err
	}
	if err := writeFileAtomic(dst, b, 0o644); err != nil {
		return nil, err
	}
	return b, nil
}

// backupShared copies a shared file into .vibekb/backups/ before VibeKB edits it,
// returning the backup's repo-relative path (or "" if the source was absent).
func backupShared(target, relPath string) (string, error) {
	srcAbs := filepath.Join(target, filepath.FromSlash(relPath))
	b, err := os.ReadFile(srcAbs)
	if err != nil {
		return "", nil // nothing to back up
	}
	flat := strings.ReplaceAll(strings.TrimPrefix(relPath, "./"), "/", "_")
	stamp := time.Now().UTC().Format("20060102T150405Z")
	rel := ".vibekb/backups/" + flat + "." + stamp + ".bak"
	dst := filepath.Join(target, filepath.FromSlash(rel))
	if err := writeFileAtomic(dst, b, 0o644); err != nil {
		return "", err
	}
	return rel, nil
}

// applyResult is the outcome of applying a plan.
type applyResult struct {
	files       []fileRecord
	backups     []string
	conflicts   []string // human-readable adapter conflicts (managed blocks)
	copiedFiles int
}

// applyPlan performs the writes described by p, transaction-style: payload and
// namespaced files are written atomically; shared files are backed up before a
// managed block is inserted/updated; the install manifest is written last by the
// caller. Managed-block conflicts are reported and skipped, never forced.
func applyPlan(c *console, m manifest, target string, p plan) (applyResult, bool) {
	var res applyResult
	ok := true

	// ---- payload -----------------------------------------------------------
	for _, op := range p.payload {
		dst := filepath.Join(target, filepath.FromSlash(op.dest))
		b, err := writeEmbeddedAtomic(op.src, dst)
		if err != nil {
			c.errf("failed to write %s: %v", op.dest, err)
			return res, false
		}
		res.copiedFiles++
		res.files = append(res.files, fileRecord{
			Path: op.dest, Ownership: ownVibeKB, Kind: "payload",
			InstalledHash: sha256Hex(b), PreExisting: op.preExisting, WholeFile: true,
		})
	}

	// ---- adapters ----------------------------------------------------------
	for _, op := range p.adapters {
		switch op.kind {
		case "namespaced":
			b, err := writeEmbeddedAtomic(op.src, filepath.Join(target, filepath.FromSlash(op.dest)))
			if err != nil {
				c.errf("failed to write %s: %v", op.dest, err)
				return res, false
			}
			res.files = append(res.files, fileRecord{
				Path: op.dest, Ownership: ownVibeKB, Kind: "namespaced", Integration: op.name,
				InstalledHash: sha256Hex(b), PreExisting: op.preExisting, WholeFile: true,
			})
		case "managed-block":
			if op.outcome.Action == blockConflict {
				res.conflicts = append(res.conflicts, op.dest+": "+op.outcome.Detail)
				continue
			}
			if op.outcome.Changed {
				if op.preExisting {
					bk, err := backupShared(target, op.dest)
					if err != nil {
						c.warn("could not back up " + op.dest + ": " + err.Error())
					} else if bk != "" {
						res.backups = append(res.backups, bk)
					}
				}
				if err := writeFileAtomic(filepath.Join(target, filepath.FromSlash(op.dest)), op.outcome.Content, 0o644); err != nil {
					c.errf("failed to write %s: %v", op.dest, err)
					return res, false
				}
			}
			// Record the managed block regardless of change so uninstall can find it.
			block := extractBlock(op.outcome.Content)
			res.files = append(res.files, fileRecord{
				Path: op.dest, Ownership: ownShared, Kind: "managed-block", Integration: op.name,
				BlockHash: blockHash(block), BlockVersion: m.blockVersion(),
				PreExisting: op.preExisting, WholeFile: false,
			})
		}
	}

	return res, ok
}

// extractBlock returns the managed block substring (markers included) from
// content, or "" if none is present.
func extractBlock(content []byte) string {
	s := string(content)
	si := strings.Index(s, blockStartPrefix)
	if si < 0 {
		return ""
	}
	ei := strings.Index(s[si:], blockEndMarker)
	if ei < 0 {
		return ""
	}
	return s[si : si+ei+len(blockEndMarker)]
}
