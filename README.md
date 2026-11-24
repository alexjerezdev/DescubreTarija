# 🔥 OPERACIÓN FOGÓN · PHP desde cero

**Tecnología Web I · UPDS Tarija · FORJA WEB 2026**
M.Sc. Ing. Juan Pablo Cruz Ovando · jpcruzovando.com

Hoy el cocinero entra a la cocina: **Apache** es el mozo, **PHP** el cocinero y **MySQL** la despensa.

## Encender (Windows)
1. Abrí **Docker Desktop** (ballena verde).
2. En esta carpeta: clic derecho → **Abrir en Terminal** y escribí:
   ```powershell
   Set-ExecutionPolicy -Scope Process Bypass
   .\scripts\iniciar.ps1
   ```
3. Abrí **http://localhost:8090**.

| Qué | Dónde |
|---|---|
| La cocina (tus páginas) | http://localhost:8090 |
| La despensa (phpMyAdmin) | http://localhost:8091 · usuario `fogon` · clave `fogon123` |
| Tus archivos | carpeta `www` (abrila con VS Code) |
| MySQL desde Workbench | `127.0.0.1:3308` · `fogon` / `fogon123` |

## Los niveles
| Carpeta | Nivel | Qué aprendés |
|---|---|---|
| `www/00-gancho` | 0 | Por qué PHP: HTML estático vs. página viva |
| `www/01-hablar` | 1 | `echo`, variables, comillas, cuentas |
| `www/02-decidir` | 2 | `if`, arrays, `foreach` |
| `www/03-escuchar` | 3 | Formularios: `$_GET` y `$_POST` |
| `www/04-recordar` | 4 | MySQL con PDO: leer (`SELECT`) y guardar (`INSERT`) |
| `www/05-errores` | Museo | Los 5 errores que todos cometen |
| `www/06-reto-final` | Jefe final | Formulario de contacto para tu proyecto |
| `www/soluciones` | — | Soluciones de los retos (¡intentá antes!) |

## Comandos útiles
```powershell
docker compose ps                 # ¿qué está encendido?
docker compose logs web           # ¿qué dice el servidor?
.\scripts\reiniciar-base.ps1      # volver la base de datos a cero
.\scripts\apagar.ps1              # apagar (tus archivos quedan)
.\scripts\plan-b-sin-base.ps1     # plan B: solo PHP, sin MySQL (niveles 0 a 3)
```

## Si algo falla
- **El puerto 8090, 8091 o 3308 está ocupado:** cambiá el número de la izquierda en `docker-compose.yml` (por ejemplo, `"8095:80"`).
- **"La despensa está cerrada":** la base tarda unos segundos en arrancar la primera vez. Esperá y recargá.
- **Cambié `db/init.sql` y no pasa nada:** ese archivo solo se lee la primera vez. Usá `reiniciar-base.ps1`.
