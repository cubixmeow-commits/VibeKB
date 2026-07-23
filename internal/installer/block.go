package installer

import (
	"bytes"
	"crypto/sha256"
	"encoding/hex"
	"fmt"
	"strings"
)

// Managed-block engine.
//
// VibeKB integrates with shared, user-owned files (AGENTS.md, CLAUDE.md, …) only
// through a single clearly marked block. Everything outside the block is
// preserved byte-for-byte; the block itself is idempotent (re-running install
// produces no change), updatable (only the block's body is replaced), and
// removable (uninstall deletes just the block). Line-ending style and trailing
// newline behaviour are preserved.
//
// Markers (versioned so a future format change is detectable):
//
//	<!-- VIBEKB:START v1 -->
//	…managed body…
//	<!-- VIBEKB:END -->
const (
	blockStartPrefix = "<!-- VIBEKB:START"
	blockEndMarker   = "<!-- VIBEKB:END -->"
)

// blockAction is the outcome category of an apply/remove.
type blockAction string

const (
	blockInsert   blockAction = "insert"
	blockUpdate   blockAction = "update"
	blockRemove   blockAction = "remove"
	blockNoop     blockAction = "noop"
	blockConflict blockAction = "conflict"
)

// blockOutcome reports what applying or removing a managed block would do.
type blockOutcome struct {
	Content  []byte      // resulting file bytes
	Changed  bool        // whether Content differs from the input
	HadBlock bool        // whether a VibeKB block already existed
	Action   blockAction // insert | update | remove | noop | conflict
	Detail   string      // human explanation (esp. for conflict)
}

// detectEOL returns "\r\n" if the content uses CRLF anywhere, else "\n".
func detectEOL(b []byte) string {
	if bytes.Contains(b, []byte("\r\n")) {
		return "\r\n"
	}
	return "\n"
}

// renderBlock builds the full managed block (markers + body) in the given EOL.
// The body is normalised to the target EOL and trailing blank lines are trimmed.
func renderBlock(body string, version int, eol string) string {
	start := fmt.Sprintf("%s v%d -->", blockStartPrefix, version)
	norm := strings.ReplaceAll(body, "\r\n", "\n")
	norm = strings.TrimRight(norm, "\n")
	lines := append([]string{start}, strings.Split(norm, "\n")...)
	lines = append(lines, blockEndMarker)
	return strings.Join(lines, eol)
}

// blockHash is a stable hash of a rendered block, EOL-insensitive, so the
// installer can record and compare block identity without churn on line endings.
func blockHash(block string) string {
	norm := strings.ReplaceAll(block, "\r\n", "\n")
	sum := sha256.Sum256([]byte(norm))
	return "sha256:" + hex.EncodeToString(sum[:])
}

// markerCounts returns how many start and end markers a file contains.
func markerCounts(s string) (starts, ends int) {
	return strings.Count(s, blockStartPrefix), strings.Count(s, blockEndMarker)
}

// applyManagedBlock inserts or updates VibeKB's managed block in existing,
// returning the resulting bytes and what happened. A malformed or duplicated
// marker set is reported as a conflict and never silently rewritten.
func applyManagedBlock(existing []byte, body string, version int) blockOutcome {
	s := string(existing)
	starts, ends := markerCounts(s)
	eol := detectEOL(existing)
	block := renderBlock(body, version, eol)

	switch {
	case starts == 0 && ends == 0:
		return insertBlock(existing, block, eol)
	case starts == 1 && ends == 1:
		return updateBlock(existing, block, eol)
	default:
		return blockOutcome{
			Content: existing, Changed: false, HadBlock: true, Action: blockConflict,
			Detail: fmt.Sprintf("found %d START and %d END marker(s); expected exactly one of each", starts, ends),
		}
	}
}

// insertBlock appends the block to existing, separated by one blank line and
// preserving a single trailing newline.
func insertBlock(existing []byte, block, eol string) blockOutcome {
	if len(bytes.TrimSpace(existing)) == 0 {
		out := block + eol
		return blockOutcome{Content: []byte(out), Changed: true, HadBlock: false, Action: blockInsert}
	}
	trimmed := strings.TrimRight(string(existing), "\r\n")
	out := trimmed + eol + eol + block + eol
	changed := out != string(existing)
	return blockOutcome{Content: []byte(out), Changed: changed, HadBlock: false, Action: blockInsert}
}

// updateBlock replaces the body of an existing single block in place, preserving
// content before and after and the trailing-newline convention.
func updateBlock(existing []byte, block, eol string) blockOutcome {
	s := string(existing)
	si := strings.Index(s, blockStartPrefix)
	lineStart := 0
	if idx := strings.LastIndex(s[:si], "\n"); idx >= 0 {
		lineStart = idx + 1
	}
	ei := strings.Index(s[si:], blockEndMarker)
	if ei < 0 {
		return blockOutcome{Content: existing, Changed: false, HadBlock: true, Action: blockConflict,
			Detail: "START marker without a following END marker"}
	}
	ei += si
	end := len(s)
	newlineAfter := false
	if nl := strings.Index(s[ei:], "\n"); nl >= 0 {
		end = ei + nl + 1
		newlineAfter = true
	}
	before := s[:lineStart]
	after := s[end:]
	out := before + block
	if newlineAfter {
		out += eol + after
	}
	changed := out != s
	action := blockUpdate
	if !changed {
		action = blockNoop
	}
	return blockOutcome{Content: []byte(out), Changed: changed, HadBlock: true, Action: action}
}

// removeManagedBlock deletes VibeKB's managed block from existing, preserving all
// other content and leaving a single trailing newline. A malformed or duplicated
// marker set is reported as a conflict and left untouched.
func removeManagedBlock(existing []byte) blockOutcome {
	s := string(existing)
	starts, ends := markerCounts(s)
	switch {
	case starts == 0 && ends == 0:
		return blockOutcome{Content: existing, Changed: false, HadBlock: false, Action: blockNoop}
	case starts != 1 || ends != 1:
		return blockOutcome{Content: existing, Changed: false, HadBlock: true, Action: blockConflict,
			Detail: fmt.Sprintf("found %d START and %d END marker(s); expected exactly one of each", starts, ends)}
	}
	eol := detectEOL(existing)
	si := strings.Index(s, blockStartPrefix)
	lineStart := 0
	if idx := strings.LastIndex(s[:si], "\n"); idx >= 0 {
		lineStart = idx + 1
	}
	ei := strings.Index(s[si:], blockEndMarker) + si
	end := len(s)
	if nl := strings.Index(s[ei:], "\n"); nl >= 0 {
		end = ei + nl + 1
	}
	joined := s[:lineStart] + s[end:]
	out := strings.TrimRight(joined, "\r\n")
	if out != "" {
		out += eol
	}
	changed := out != s
	return blockOutcome{Content: []byte(out), Changed: changed, HadBlock: true, Action: blockRemove}
}
