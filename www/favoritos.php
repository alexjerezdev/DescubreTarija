<?php
require __DIR__ . '/includes/conexion.php';
require_once __DIR__ . '/includes/funciones.php';

requerir_login();

$titulo = 'Mis favoritos';
$activo = 'favoritos';

// Reutiliza la consulta base y le agrega el JOIN con favoritos del usuario
$consulta = $pdo->prepare(
    SQL_DESTINOS . ' JOIN favoritos f ON f.destino_id = d.id
     WHERE f.usuario_id = ?
     ORDER BY f.guardado_en DESC, d.nombre'
);
$consulta->execute([usuario_actual()['id']]);
$destinos = $consulta->fetchAll();

require __DIR__ . '/includes/header.php';
?>

<div class="container py-5">
  <h1 class="mb-2">Mis favoritos</h1>
  <p class="text-muted"><b><?= count($destinos) ?></b> destino(s) guardado(s)</p>

  <?php if ($destinos): ?>
    <div class="row g-4">
      <?php foreach ($destinos as $d): ?>
        <?php require __DIR__ . '/includes/tarjeta_destino.php'; ?>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <div class="vacio">
      <i class="bi bi-heart"></i>
      <h3 class="mt-2">Aún no guardaste destinos</h3>
      <p>Toca el corazón de un destino para tenerlo aquí.</p>
      <a class="btn btn-dorado" href="/destinos.php">Explorar destinos</a>
    </div>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>