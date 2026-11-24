#!/usr/bin/env bash
# Borra la base de datos y la vuelve a crear desde db/init.sql (los archivos .php NO se tocan)
cd "$(dirname "$0")/.."
docker compose down -v && docker compose up -d --build
echo "  Base de datos reiniciada desde db/init.sql"
