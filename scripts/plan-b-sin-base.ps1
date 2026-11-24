# =====================================================================
#  PLAN B · si docker compose falla: solo PHP, sin base de datos.
#  Alcanza para los niveles 0, 1, 2 y 3 y el museo de errores.
# =====================================================================
Set-Location (Split-Path $PSScriptRoot -Parent)
docker run -d --name fogon-planb -p 8090:80 -v "${PWD}\www:/var/www/html" php:8.3-apache
Write-Host "  Plan B encendido: http://localhost:8090  (para apagarlo: docker rm -f fogon-planb)" -ForegroundColor Yellow
Start-Process "http://localhost:8090"
