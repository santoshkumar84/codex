#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
OUT_DIR="$ROOT_DIR/dist"
STAMP="$(date +%Y%m%d-%H%M%S)"
OUT_FILE="$OUT_DIR/codearena-${STAMP}.zip"

mkdir -p "$OUT_DIR"

cd "$ROOT_DIR"
zip -rq "$OUT_FILE" . \
  -x '.git/*' \
  -x 'dist/*' \
  -x 'submissions/runtime/*' \
  -x '__pycache__/*' \
  -x '*.log'

echo "$OUT_FILE"
