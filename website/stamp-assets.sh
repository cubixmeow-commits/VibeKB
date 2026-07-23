#!/bin/sh
# stamp-assets.sh — cache-busting for the static website.
#
# Appends a short content hash to each local asset reference in index.html
# (?v=<hash>), so a new deploy is never served from a stale browser/CDN cache
# while unchanged assets keep their URL (and stay cached). Content-addressed:
# the hash only changes when the file's bytes change.
#
# No build step and no dependencies beyond a POSIX shell and sha1sum/shasum.
# Idempotent — safe to run repeatedly. Run it after editing any CSS/JS/data
# asset and before committing:
#
#   sh website/stamp-assets.sh
#
set -eu

cd "$(dirname "$0")"
INDEX="index.html"

# Local assets referenced by index.html. Add new ones here if the page grows.
ASSETS="assets/css/site.css assets/js/map.js assets/js/site.js assets/data/model.js"

hash_of() {
  if command -v sha1sum >/dev/null 2>&1; then
    sha1sum "$1" | cut -c1-10
  else
    shasum "$1" | cut -c1-10   # macOS / BSD
  fi
}

for asset in $ASSETS; do
  [ -f "$asset" ] || { echo "skip (missing): $asset" >&2; continue; }
  h=$(hash_of "$asset")
  # Escape dots in the path for the regex; the '#' delimiter lets '/' pass through.
  pat=$(printf '%s' "$asset" | sed 's/\./\\./g')
  sed -E "s#${pat}(\?v=[0-9a-f]+)?#${asset}?v=${h}#g" "$INDEX" > "$INDEX.tmp"
  mv "$INDEX.tmp" "$INDEX"
  echo "stamped ${asset}?v=${h}"
done
