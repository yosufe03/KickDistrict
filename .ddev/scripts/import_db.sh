#!/usr/bin/env bash
set -euo pipefail

PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
SQL_FILE="${PROJECT_ROOT}/App/Database/schema.sql"
RELATIVE_SQL_FILE="App/Database/schema.sql"

if [[ ! -f "$SQL_FILE" ]]; then
  echo "[kickdistrict] SQL file not found: $SQL_FILE" >&2
  exit 1
fi

echo "[kickdistrict] Importing schema from ${RELATIVE_SQL_FILE}..."
if ddev import-db --file="$SQL_FILE"; then
  echo "[kickdistrict] Schema import complete."
else
  echo "[kickdistrict] Schema import skipped after error." >&2
fi
