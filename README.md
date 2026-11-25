# DescubreTarija

Guía turística digital del departamento de Tarija, Bolivia.
Proyecto académico de **Tecnología Web I** · Ingeniería en Sistemas · Universidad Privada Domingo Savio (UPDS).

## Qué hace
- Catálogo de destinos con buscador y filtros (nombre, categoría, ubicación).
- Detalle con galería y mapa interactivo (Leaflet + OpenStreetMap).
- Registro, login y sesiones (contraseñas con `password_hash`).
- Favoritos y planificador de recorridos con orden y mapa.
- Panel administrativo: destinos, categorías e imágenes.

## Tecnologías
PHP 8.3 (Apache) · MySQL 8.4 · PDO · Bootstrap 5 · Leaflet · Docker Compose · phpMyAdmin.

## Cómo ejecutarlo
Requisitos: Docker Desktop.

```powershell
git clone https://github.com/alexjerezdev/DescubreTarija.git
cd DescubreTarija
.\scripts\iniciar.ps1
```

| Servicio | URL |
|---|---|
| Sitio web | http://localhost:8090 |
| phpMyAdmin | http://localhost:8091 |
| MySQL (externo) | 127.0.0.1:3308 |

Usuarios de prueba: `admin@descubretarija.bo` / `Admin2026!` y `maria@correo.com` / `Demo2026!`
(solo para demostración).

Apagar: `.\scripts\apagar.ps1` · Base desde cero: `.\scripts\reiniciar-base.ps1`

## Estructura
- `www/` páginas PHP, `includes/` piezas comunes, `admin/` panel, `assets/` estilos e imágenes
- `db/init.sql` esquema y datos iniciales (7 tablas)
- `bd/` consultas de la tarea y respaldo
- `scripts/` encendido, apagado y pruebas

## Datos turísticos
Descripciones, ubicaciones y coordenadas se cargan solo cuando están verificadas con fuentes confiables.
El mapa muestra únicamente destinos con coordenadas verificadas.

## Autor
Alex Jerez · UPDS Tarija · 2026