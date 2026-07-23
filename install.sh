#!/bin/sh
# VibeKB CLI installer — served from https://iainreid.dev/vibekb/install.sh
#
# Installs the latest vibekb release binary for this machine. GitHub Releases
# is the download source; this website is the product entry point. Future
# enhancements (checksum verification, code signing checks, Homebrew/Winget
# hints) should land here without changing the user-facing curl | sh command.
#
# Usage:
#   curl -fsSL https://iainreid.dev/vibekb/install.sh | sh
#
# Optional environment overrides (for testing / power users):
#   VIBEKB_INSTALL_DIR   Force install directory
#   VIBEKB_REPO          GitHub owner/name (default: cubixmeow-commits/VibeKB)
#   VIBEKB_BASE_URL      Override release download base (testing)

set -eu

REPO="${VIBEKB_REPO:-cubixmeow-commits/VibeKB}"
RELEASES_API="https://api.github.com/repos/${REPO}/releases/latest"
DOWNLOAD_BASE="${VIBEKB_BASE_URL:-https://github.com/${REPO}/releases/latest/download}"
BIN_NAME="vibekb"

# --- output helpers ----------------------------------------------------------

if [ -t 1 ] || [ "${FORCE_COLOR:-}" = "1" ]; then
  C_RESET="$(printf '\033[0m')"
  C_BOLD="$(printf '\033[1m')"
  C_DIM="$(printf '\033[2m')"
  C_GREEN="$(printf '\033[32m')"
  C_YELLOW="$(printf '\033[33m')"
  C_RED="$(printf '\033[31m')"
  C_CYAN="$(printf '\033[36m')"
else
  C_RESET=""; C_BOLD=""; C_DIM=""; C_GREEN=""; C_YELLOW=""; C_RED=""; C_CYAN=""
fi

ok()   { printf '%s✓%s %s\n' "$C_GREEN" "$C_RESET" "$*"; }
info() { printf '%s…%s %s\n' "$C_CYAN" "$C_RESET" "$*"; }
warn() { printf '%s!%s %s\n' "$C_YELLOW" "$C_RESET" "$*"; }
die()  {
  printf '%s✗%s %s\n' "$C_RED" "$C_RESET" "$*" >&2
  exit 1
}

need_cmd() {
  if ! command -v "$1" >/dev/null 2>&1; then
    die "Missing required command: $1
Install $1 and re-run:
  curl -fsSL https://iainreid.dev/vibekb/install.sh | sh"
  fi
}

# --- platform detection ------------------------------------------------------

detect_platform() {
  os="$(uname -s 2>/dev/null || echo unknown)"
  arch="$(uname -m 2>/dev/null || echo unknown)"

  case "$os" in
    Darwin)  GOOS="darwin";  OS_LABEL="macOS" ;;
    Linux)   GOOS="linux";   OS_LABEL="Linux" ;;
    MINGW*|MSYS*|CYGWIN*|Windows_NT)
      die "Windows is not supported by this installer yet.
Download a Windows binary from:
  https://github.com/${REPO}/releases/latest
Or use WSL and re-run this installer from Linux."
      ;;
    *)
      die "Unsupported operating system: $os
This installer supports macOS and Linux.
Manual binaries: https://github.com/${REPO}/releases/latest"
      ;;
  esac

  case "$arch" in
    arm64|aarch64) GOARCH="arm64"; ARCH_LABEL="Apple Silicon" ;;
    x86_64|amd64)  GOARCH="amd64"; ARCH_LABEL="Intel/AMD 64-bit" ;;
    *)
      die "Unsupported CPU architecture: $arch
Supported: arm64 (Apple Silicon) and amd64 (Intel/AMD).
Manual binaries: https://github.com/${REPO}/releases/latest"
      ;;
  esac

  # On Linux arm64, "Apple Silicon" label is wrong.
  if [ "$GOOS" = "linux" ] && [ "$GOARCH" = "arm64" ]; then
    ARCH_LABEL="ARM64"
  elif [ "$GOOS" = "linux" ] && [ "$GOARCH" = "amd64" ]; then
    ARCH_LABEL="x86_64"
  elif [ "$GOOS" = "darwin" ] && [ "$GOARCH" = "amd64" ]; then
    ARCH_LABEL="Intel"
  fi

  ASSET="${BIN_NAME}-${GOOS}-${GOARCH}"
}

# --- download helpers --------------------------------------------------------

http_get() {
  # $1 = URL, $2 = output path (optional; stdout if omitted)
  url="$1"
  out="${2:-}"
  if command -v curl >/dev/null 2>&1; then
    if [ -n "$out" ]; then
      curl -fsSL --proto '=https' --tlsv1.2 -o "$out" "$url"
    else
      curl -fsSL --proto '=https' --tlsv1.2 "$url"
    fi
  elif command -v wget >/dev/null 2>&1; then
    if [ -n "$out" ]; then
      wget -qO "$out" "$url"
    else
      wget -qO- "$url"
    fi
  else
    die "Need curl or wget to download VibeKB.
Install curl, then re-run:
  curl -fsSL https://iainreid.dev/vibekb/install.sh | sh"
  fi
}

latest_tag() {
  # Best-effort; install still works if this fails (latest/download URL).
  json="$(http_get "$RELEASES_API" 2>/dev/null || true)"
  if [ -z "$json" ]; then
    echo ""
    return 0
  fi
  echo "$json" | sed -n 's/.*"tag_name"[[:space:]]*:[[:space:]]*"\([^"]*\)".*/\1/p' | head -n 1
}

# --- install location --------------------------------------------------------

resolve_install_dir() {
  if [ -n "${VIBEKB_INSTALL_DIR:-}" ]; then
    INSTALL_DIR="$VIBEKB_INSTALL_DIR"
    return 0
  fi

  if [ -d /usr/local/bin ] && [ -w /usr/local/bin ]; then
    INSTALL_DIR="/usr/local/bin"
    return 0
  fi

  # Try creating /usr/local/bin only when we clearly own the parent and it is writable.
  if [ -w /usr/local ] 2>/dev/null; then
    mkdir -p /usr/local/bin 2>/dev/null || true
    if [ -d /usr/local/bin ] && [ -w /usr/local/bin ]; then
      INSTALL_DIR="/usr/local/bin"
      return 0
    fi
  fi

  INSTALL_DIR="${HOME}/.local/bin"
  mkdir -p "$INSTALL_DIR"
}

path_contains_dir() {
  needle="$1"
  echo ":${PATH}:" | grep -q ":${needle}:"
}

# --- main --------------------------------------------------------------------

main() {
  printf '%s\n' "${C_BOLD}Installing VibeKB…${C_RESET}"

  need_cmd uname
  need_cmd mktemp
  need_cmd chmod
  need_cmd mv

  info "Detecting platform…"
  detect_platform
  ok "${OS_LABEL} (${ARCH_LABEL})"

  info "Finding latest release…"
  TAG="$(latest_tag || true)"
  if [ -n "$TAG" ]; then
    ok "Latest release: ${TAG}"
  else
    ok "Using GitHub latest release redirect"
  fi

  TMPDIR_INSTALL="$(mktemp -d 2>/dev/null || mktemp -d -t vibekb-install)"
  trap 'rm -rf "$TMPDIR_INSTALL"' EXIT INT HUP TERM

  DEST_TMP="${TMPDIR_INSTALL}/${BIN_NAME}"
  URL="${DOWNLOAD_BASE}/${ASSET}"

  info "Downloading ${ASSET}…"
  if ! http_get "$URL" "$DEST_TMP"; then
    die "Download failed for:
  ${URL}

Check that a release exists and includes ${ASSET}:
  https://github.com/${REPO}/releases/latest

If the network blocks GitHub, download the binary manually and place it on your PATH."
  fi

  # Reject empty / HTML error pages masquerading as binaries.
  size="$(wc -c < "$DEST_TMP" | tr -d ' ')"
  if [ "${size:-0}" -lt 1000 ]; then
    die "Downloaded file is too small (${size} bytes) — likely not a binary.
URL: ${URL}
Manual install: https://github.com/${REPO}/releases/latest"
  fi

  chmod +x "$DEST_TMP"

  info "Installing…"
  resolve_install_dir
  TARGET="${INSTALL_DIR}/${BIN_NAME}"

  if ! mv -f "$DEST_TMP" "$TARGET" 2>/dev/null; then
    # Cross-device mv can fail; fall back to cp.
    cp -f "$DEST_TMP" "$TARGET" || die "Could not write ${TARGET}
Try:
  VIBEKB_INSTALL_DIR=\$HOME/.local/bin curl -fsSL https://iainreid.dev/vibekb/install.sh | sh
Or download manually: https://github.com/${REPO}/releases/latest"
    chmod +x "$TARGET"
  fi
  ok "Installed to ${TARGET}"

  if ! path_contains_dir "$INSTALL_DIR"; then
    warn "${INSTALL_DIR} is not on your PATH."
    printf '  Add this to your shell profile, then open a new terminal:\n'
    printf '    export PATH="%s:$PATH"\n' "$INSTALL_DIR"
  fi

  info "Verifying…"
  VERIFY_BIN="$TARGET"
  if command -v "$BIN_NAME" >/dev/null 2>&1; then
    # Prefer PATH resolution when available (matches user experience).
    VERIFY_BIN="$BIN_NAME"
  fi

  if ! version_out="$("$VERIFY_BIN" version 2>&1)"; then
    die "vibekb was installed to ${TARGET} but \`vibekb version\` failed:
${version_out}

Ensure ${INSTALL_DIR} is on your PATH and the binary is executable."
  fi
  ok "Verified"
  printf '%s\n' "${C_DIM}${version_out}${C_RESET}" | sed 's/^/  /'

  printf '\n%s✓ VibeKB installed successfully!%s\n' "$C_GREEN" "$C_RESET"
  printf '\n%sNext:%s\n' "$C_BOLD" "$C_RESET"
  printf '  cd your-project\n'
  printf '  vibekb install .\n'
  printf '\n%sThen ask your coding agent to build the first model using prompts/INTEGRATE_VIBEKB.md.%s\n' "$C_DIM" "$C_RESET"
}

main "$@"
