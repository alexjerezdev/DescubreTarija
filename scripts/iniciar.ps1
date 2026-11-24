# =====================================================================
#  OPERACIÓN FOGÓN · encender la cocina (Windows / PowerShell)
#  Uso:  .\scripts\iniciar.ps1
#  Si Windows bloquea el script:  Set-ExecutionPolicy -Scope Process Bypass
# =====================================================================
Set-Location (Split-Path $PSScriptRoot -Parent)
Write-Host ""
Write-Host "  OPERACION FOGON · encendiendo la cocina..." -ForegroundColor Yellow
Write-Host "  (la primera vez descarga imagenes: puede tardar unos minutos)" -ForegroundColor DarkGray
docker compose up -d --build
if ($LASTEXITCODE -ne 0) {
    Write-Host "  No pude encender la cocina. ¿Docker Desktop esta abierto (ballena verde)?" -ForegroundColor Red
    exit 1
}
Write-Host "  Esperando a que la despensa (MySQL) este lista..." -ForegroundColor DarkGray
for ($i = 0; $i -lt 60; $i++) {
    $estado = docker inspect -f "{{.State.Health.Status}}" fogon-db 2>$null
    if ($estado -eq "healthy") { break }
    Start-Sleep -Seconds 2
}
Write-Host ""
Write-Host "  LISTO. Abri en el navegador:" -ForegroundColor Green
Write-Host "    La cocina ........ http://localhost:8090" -ForegroundColor Cyan
Write-Host "    La despensa ...... http://localhost:8091  (phpMyAdmin)" -ForegroundColor Cyan
Write-Host "    Tus archivos ..... carpeta www (abrila con VS Code)" -ForegroundColor Cyan
Write-Host ""
Start-Process "http://localhost:8090"
