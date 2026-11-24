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