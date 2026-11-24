#!/usr/bin/env bash
# OPERACIÓN FOGÓN · encender la cocina (Linux / macOS)
set -e
cd "$(dirname "$0")/.."
echo "  OPERACIÓN FOGÓN · encendiendo la cocina..."
docker compose up -d --build
echo -n "  Esperando a la despensa (MySQL)"
for _ in $(seq 1 60); do
  [ "$(docker inspect -f '{{.State.Health.Status}}' fogon-db 2>/dev/null)" = "healthy" ] && break
  echo -n "."; sleep 2
done
echo
echo "  LISTO:  cocina http://localhost:8090   ·   despensa (phpMyAdmin) http://localhost:8091"
