<?php
// Funciones compartidas. Inicia la sesión una sola vez.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Escudo anti-XSS: usar en TODO dato que se muestre en pantalla
function e($texto): string
{
    return htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8');
}

// ---- Sesión ----
function esta_logueado(): bool
{
    return isset($_SESSION['usuario']);
}

function usuario_actual(): ?array
{
    return $_SESSION['usuario'] ?? null;
}

function es_admin(): bool
{
    return esta_logueado() && ($_SESSION['usuario']['rol'] ?? '') === 'admin';
}

function requerir_login(): void
{
    if (!esta_logueado()) {
        poner_flash('warning', 'Inicia sesión para continuar.');
        header('Location: /login.php');
        exit;
    }
}

function requerir_admin(): void
{
    if (!es_admin()) {
        poner_flash('danger', 'Solo el administrador puede entrar aquí.');
        header('Location: /login.php');
        exit;
    }
}

// ---- Mensajes flash (se muestran una vez y desaparecen) ----
// tipo: success | danger | warning | info
function poner_flash(string $tipo, string $mensaje): void
{
    $_SESSION['flash'] = ['tipo' => $tipo, 'mensaje' => $mensaje];
}

function mostrar_flash(): void
{
    if (!isset($_SESSION['flash'])) {
        return;
    }
    $f = $_SESSION['flash'];
    unset($_SESSION['flash']);
    echo '<div class="container mt-3"><div class="alert alert-' . e($f['tipo']) . ' alert-dismissible fade show" role="alert">';
    echo e($f['mensaje']);
    echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button></div></div>';
}

// ---- Navbar: marca el enlace de la página actual ----
function nav_activo(string $pagina, string $activo): string
{
    return $pagina === $activo ? ' active' : '';
}



// ---- Consulta base de destinos (con categoría y primera foto) ----
// Uso: $pdo->query(SQL_DESTINOS . ' WHERE d.destacado = 1 ORDER BY d.nombre')
const SQL_DESTINOS = 'SELECT d.id, d.nombre, d.descripcion, d.ubicacion, d.categoria_id,
        c.nombre AS categoria,
        (SELECT i.url FROM imagenes i WHERE i.destino_id = d.id ORDER BY i.id LIMIT 1) AS foto
    FROM destinos d
    LEFT JOIN categorias c ON c.id = d.categoria_id';

// Si el dato es NULL, vacío o "POR VERIFICAR", devuelve un texto alternativo
function dato($texto, string $vacio = 'Por verificar'): string
{
    $texto = trim((string) $texto);
    return ($texto === '' || strcasecmp($texto, 'POR VERIFICAR') === 0) ? $vacio : $texto;
}

// Devuelve la URL de la foto SOLO si el archivo existe en www/ (si no, null)
function foto_url(?string $ruta): ?string
{
    if (!$ruta || str_contains($ruta, '..')) {
        return null;
    }
    $ruta = ltrim($ruta, '/');
    return is_file(__DIR__ . '/../' . $ruta) ? '/' . $ruta : null;
}