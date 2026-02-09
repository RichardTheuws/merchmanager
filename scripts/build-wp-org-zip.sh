#!/usr/bin/env bash
# Build a WordPress.org-ready plugin ZIP (max 10MB).
# Excludes vendor (Composer dev deps only - not used at runtime), tests, docs, and dev files.

set -e
PLUGIN_SLUG="merchmanager"
VERSION="${1:-1.0.4}"
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
BUILD_DIR="$ROOT/build-zip"
ZIP_NAME="${PLUGIN_SLUG}-${VERSION}.zip"

cd "$ROOT"
rm -rf "$BUILD_DIR"
# Remove any existing plugin ZIPs in project root so only the new one remains
rm -f "${PLUGIN_SLUG}"-*.zip
mkdir -p "$BUILD_DIR/$PLUGIN_SLUG"

rsync -a \
  --exclude='.git' \
  --exclude='.cursor' \
  --exclude='node_modules' \
  --exclude='vendor' \
  --exclude='build-zip' \
  --exclude='*.zip' \
  --exclude='.DS_Store' \
  --exclude='tests' \
  --exclude='docs' \
  --exclude='.github' \
  --exclude='cypress.config.js' \
  --exclude='phpunit.xml' \
  --exclude='Dockerfile' \
  --exclude='Dockerfile.test' \
  --exclude='docker-compose.yml' \
  --exclude='.editorconfig' \
  --exclude='.stylelintrc.js' \
  --exclude='.gitattributes' \
  --exclude='.gitignore' \
  --exclude='composer.json' \
  --exclude='composer.lock' \
  --exclude='package.json' \
  --exclude='package-lock.json' \
  --exclude='src' \
  --exclude='scripts' \
  --exclude='ROADMAP.md' \
  --exclude='IMPROVEMENTS.md' \
  . "$BUILD_DIR/$PLUGIN_SLUG/"

cd "$BUILD_DIR"
zip -r "$ROOT/$ZIP_NAME" "$PLUGIN_SLUG"
cd "$ROOT"
rm -rf "$BUILD_DIR"

SIZE=$(du -h "$ZIP_NAME" | cut -f1)
echo "Built $ZIP_NAME ($SIZE)"
