package installer

import (
	"bufio"
	"fmt"
	"os"
	"strings"
)

// console renders the installer's human-facing output, with optional ANSI colour
// when stdout is a terminal and NO_COLOR is unset.
type console struct {
	color bool
	in    *bufio.Reader
}

func newConsole() *console {
	return &console{
		color: isTerminal(os.Stdout) && os.Getenv("NO_COLOR") == "",
		in:    bufio.NewReader(os.Stdin),
	}
}

func (c *console) banner() {
	fmt.Println()
	fmt.Println(c.c("VibeKB installer", "1;36"))
	fmt.Println(strings.Repeat("=", 60))
	fmt.Println()
}

func (c *console) section(t string) {
	fmt.Println()
	fmt.Println(c.c(t, "1"))
	fmt.Println(strings.Repeat("-", 60))
}

func (c *console) kv(k, v string) { fmt.Printf("  %-20s: %s\n", k, v) }
func (c *console) line(s string)  { fmt.Println(s) }
func (c *console) blank()         { fmt.Println() }
func (c *console) ok(s string)    { fmt.Println("  " + c.c("✓", "32") + " " + s) }
func (c *console) warn(s string)  { fmt.Println("  " + c.c("!", "33") + " " + s) }
func (c *console) errf(format string, a ...any) {
	fmt.Fprintln(os.Stderr, "  "+c.c("✗", "31")+" "+fmt.Sprintf(format, a...))
}

// confirm prompts for a yes/no answer. On a non-interactive stdin it returns the
// default.
func (c *console) confirm(q string, def bool) bool {
	hint := "[y/N]"
	if def {
		hint = "[Y/n]"
	}
	fmt.Print(q + " " + hint + " ")
	if !isTerminal(os.Stdin) {
		fmt.Println()
		return def
	}
	line, err := c.in.ReadString('\n')
	if err != nil {
		fmt.Println()
		return def
	}
	line = strings.ToLower(strings.TrimSpace(line))
	if line == "" {
		return def
	}
	return line == "y" || line == "yes"
}

func (c *console) c(s, code string) string {
	if !c.color {
		return s
	}
	return "\033[" + code + "m" + s + "\033[0m"
}

func isTerminal(f *os.File) bool {
	info, err := f.Stat()
	return err == nil && (info.Mode()&os.ModeCharDevice) != 0
}
