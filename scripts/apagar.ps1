# Apagar la cocina (tus archivos y la base de datos se conservan)
Set-Location (Split-Path $PSScriptRoot -Parent)
docker compose down
Write-Host "  Cocina apagada. Tus archivos y tu base de datos siguen guardados." -ForegroundColor Green
