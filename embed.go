// Package vibekb is the module-root package. Its sole job is to embed the
// installation payload into the `vibekb` binary so that `vibekb install` is fully
// native — it copies the VibeKB runtime and scaffolds a fresh .vibekb/ workspace
// without executing PHP and without the source repository remaining on disk.
//
// The embed directives must live at the module root because Go's embed patterns
// cannot escape the embedding file's directory (no "..").
//
// What is embedded is exactly the installer payload declared in
// template/manifest.json plus the canonical starter definition
// (template/starter). The manifest remains the single source of truth for which
// of these paths are copied; this file only makes the bytes available.
package vibekb

import "embed"

// PayloadFS holds the installable VibeKB runtime, the installer manifest, and the
// language-neutral starter workspace definition. Paths inside it are
// repository-root-relative with forward slashes (e.g. "guide/index.php",
// "template/starter/starter.json").
//
//go:embed template/manifest.json
//go:embed all:template/starter
//go:embed all:template/integrations
//go:embed all:guide
//go:embed all:tools
//go:embed all:prompts
//go:embed PRODUCT.md SCHEMA.md INITIALIZE.md MAINTENANCE.md INSTALLER.md
var PayloadFS embed.FS
