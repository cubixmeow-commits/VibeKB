package installer

import (
	"strings"
	"testing"
)

const blkBody = "## VibeKB\n\nPointer into .vibekb/.\n"

func TestBlockInsertIntoExistingPreservesContent(t *testing.T) {
	orig := "# My Project\n\nHand-written instructions.\n"
	out := applyManagedBlock([]byte(orig), blkBody, 1)
	if !out.Changed || out.Action != blockInsert {
		t.Fatalf("expected insert, got %v changed=%v", out.Action, out.Changed)
	}
	s := string(out.Content)
	if !strings.HasPrefix(s, orig) {
		t.Fatalf("original content not preserved at head:\n%q", s)
	}
	if !strings.Contains(s, blockStartPrefix) || !strings.Contains(s, blockEndMarker) {
		t.Fatal("markers missing after insert")
	}
}

func TestBlockInsertIsIdempotent(t *testing.T) {
	orig := "# My Project\n"
	first := applyManagedBlock([]byte(orig), blkBody, 1)
	second := applyManagedBlock(first.Content, blkBody, 1)
	if second.Changed {
		t.Fatalf("second apply changed the file (not idempotent):\n%q", second.Content)
	}
	if second.Action != blockNoop {
		t.Fatalf("expected noop on re-apply, got %v", second.Action)
	}
	if strings.Count(string(second.Content), blockStartPrefix) != 1 {
		t.Fatal("duplicate block after re-apply")
	}
}

func TestBlockUpdateReplacesOnlyTheBody(t *testing.T) {
	orig := "# Head\n\n" + renderBlock("old body", 1, "\n") + "\n\n# Tail\n"
	out := applyManagedBlock([]byte(orig), "new body", 1)
	if !out.Changed || out.Action != blockUpdate {
		t.Fatalf("expected update, got %v changed=%v", out.Action, out.Changed)
	}
	s := string(out.Content)
	if !strings.Contains(s, "# Head") || !strings.Contains(s, "# Tail") {
		t.Fatalf("surrounding content lost:\n%q", s)
	}
	if strings.Contains(s, "old body") || !strings.Contains(s, "new body") {
		t.Fatalf("body not replaced:\n%q", s)
	}
}

func TestBlockCRLFPreserved(t *testing.T) {
	orig := "# Project\r\n\r\nWindows file.\r\n"
	out := applyManagedBlock([]byte(orig), blkBody, 1)
	if !out.Changed {
		t.Fatal("expected change")
	}
	if strings.Contains(strings.ReplaceAll(string(out.Content), "\r\n", ""), "\n") {
		t.Fatalf("LF leaked into a CRLF file:\n%q", out.Content)
	}
	if !strings.Contains(string(out.Content), "\r\n"+blockEndMarker) {
		t.Fatalf("end marker not CRLF-terminated:\n%q", out.Content)
	}
}

func TestBlockNoFinalNewlinePreservedOnUpdate(t *testing.T) {
	// A file whose block is at EOF with no trailing newline.
	orig := "intro\n\n" + renderBlock("old", 1, "\n")
	if strings.HasSuffix(orig, "\n") {
		t.Fatal("test fixture should not end in newline")
	}
	out := applyManagedBlock([]byte(orig), "new", 1)
	if strings.HasSuffix(string(out.Content), "\n") {
		t.Fatalf("update introduced a trailing newline:\n%q", out.Content)
	}
}

func TestBlockDuplicateMarkersAreConflict(t *testing.T) {
	orig := renderBlock("a", 1, "\n") + "\n\n" + renderBlock("b", 1, "\n") + "\n"
	out := applyManagedBlock([]byte(orig), blkBody, 1)
	if out.Action != blockConflict || out.Changed {
		t.Fatalf("expected conflict + no change, got %v changed=%v", out.Action, out.Changed)
	}
}

func TestBlockMalformedMissingEndIsConflict(t *testing.T) {
	orig := "# Head\n" + blockStartPrefix + " v1 -->\nbody without end\n"
	out := applyManagedBlock([]byte(orig), blkBody, 1)
	if out.Action != blockConflict || out.Changed {
		t.Fatalf("expected conflict, got %v changed=%v", out.Action, out.Changed)
	}
}

func TestBlockInsertIntoEmptyFile(t *testing.T) {
	out := applyManagedBlock([]byte(""), blkBody, 1)
	if !out.Changed || out.Action != blockInsert {
		t.Fatalf("expected insert into empty file, got %v", out.Action)
	}
	if !strings.HasSuffix(string(out.Content), blockEndMarker+"\n") {
		t.Fatalf("unexpected content:\n%q", out.Content)
	}
}

func TestBlockRemovePreservesOutsideContent(t *testing.T) {
	orig := "# My Project\n\nMine.\n"
	inserted := applyManagedBlock([]byte(orig), blkBody, 1)
	removed := removeManagedBlock(inserted.Content)
	if removed.Action != blockRemove || !removed.Changed {
		t.Fatalf("expected remove, got %v", removed.Action)
	}
	if !strings.Contains(string(removed.Content), "# My Project") ||
		!strings.Contains(string(removed.Content), "Mine.") {
		t.Fatalf("outside content lost on remove:\n%q", removed.Content)
	}
	if strings.Contains(string(removed.Content), blockStartPrefix) {
		t.Fatal("block not removed")
	}
}

func TestBlockRemoveConflictLeavesUntouched(t *testing.T) {
	orig := renderBlock("a", 1, "\n") + "\n" + renderBlock("b", 1, "\n") + "\n"
	out := removeManagedBlock([]byte(orig))
	if out.Action != blockConflict || out.Changed {
		t.Fatalf("expected conflict + untouched, got %v changed=%v", out.Action, out.Changed)
	}
}
