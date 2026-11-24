# Borra la base de datos y la vuelve a crear desde db/init.sql (los archivos .php NO se tocan)
Set-Location (Split-Path $PSScriptRoot -Parent)
docker compose down -v
docker compose up -d --build
Write-Host "  Base de datos reiniciada desde db/init.sql" -ForegroundColor Green
