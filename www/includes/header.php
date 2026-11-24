<?php
// Cabecera común. La página define ANTES: $titulo y $activo.
require_once __DIR__ . '/funciones.php';

$titulo  = $titulo  ?? 'DescubreTarija';
$activo  = $activo  ?? '';
$usuario = usuario_actual();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($titulo) ?> · DescubreTarija</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link href="/assets/css/tarija.css" rel="stylesheet">
    <?= $extra_head ?? '' ?>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-tarija sticky-top">
  <div class="container">
    <a class="navbar-brand logo" href="/index.php">Descubre<span>Tarija</span></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuPrincipal"
            aria-controls="menuPrincipal" aria-expanded="false" aria-label="Abrir menú">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="menuPrincipal">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link<?= nav_activo('inicio', $activo) ?>" href="/index.php">Inicio</a></li>
        <li class="nav-item"><a class="nav-link<?= nav_activo('destinos', $activo) ?>" href="/destinos.php">Destinos</a></li>
        <li class="nav-item"><a class="nav-link<?= nav_activo('mapa', $activo) ?>" href="/mapa.php">Mapa</a></li>
        <?php if (esta_logueado()): ?>
          <li class="nav-item"><a class="nav-link<?= nav_activo('favoritos', $activo) ?>" href="/favoritos.php">Favoritos</a></li>
          <li class="nav-item"><a class="nav-link<?= nav_activo('recorridos', $activo) ?>" href="/recorridos.php">Mis recorridos</a></li>
        <?php endif; ?>
        <?php if (es_admin()): ?>
          <li class="nav-item"><a class="nav-link<?= nav_activo('admin', $activo) ?>" href="/admin/index.php">Admin</a></li>
        <?php endif; ?>
      </ul>

      <div class="d-flex align-items-center gap-2">
        <?php if ($usuario): ?>
          <span class="text-white-50 small"><i class="bi bi-person-circle"></i> <?= e($usuario['nombre']) ?></span>
          <a class="btn btn-sm btn-outline-light" href="/logout.php">Salir</a>
        <?php else: ?>
          <a class="btn btn-sm btn-outline-light" href="/login.php">Ingresar</a>
          <a class="btn btn-sm btn-dorado" href="/registro.php">Registrarme</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>

<?php mostrar_flash(); ?>

<main>