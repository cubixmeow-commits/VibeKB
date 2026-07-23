package cli

import (
	"bytes"
	"io"
	"os"
	"strings"
	"testing"
)

func TestCmdVersionShape(t *testing.T) {
	old := os.Stdout
	r, w, err := os.Pipe()
	if err != nil {
		t.Fatal(err)
	}
	os.Stdout = w
	code := cmdVersion()
	_ = w.Close()
	os.Stdout = old
	var buf bytes.Buffer
	_, _ = io.Copy(&buf, r)
	_ = r.Close()

	if code != 0 {
		t.Fatalf("exit code %d", code)
	}
	out := buf.String()
	for _, want := range []string{
		"VibeKB\n",
		"Version: ",
		"Commit: ",
		"Built: ",
		"Platform: ",
	} {
		if !strings.Contains(out, want) {
			t.Errorf("version output missing %q\n---\n%s", want, out)
		}
	}
	lines := strings.Split(strings.TrimRight(out, "\n"), "\n")
	if len(lines) < 5 {
		t.Fatalf("expected at least 5 lines, got %d:\n%s", len(lines), out)
	}
	if lines[0] != "VibeKB" {
		t.Errorf("first line = %q, want VibeKB", lines[0])
	}
}
