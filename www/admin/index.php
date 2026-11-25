<?php
require __DIR__ . '/../includes/conexion.php';
require_once __DIR__ . '/../includes/funciones.php';

requerir_admin();

$titulo       = 'Panel administrativo';
$activo       = 'admin';
$admin_activo = 'dashboard';

$n = $pdo->query(
    'SELECT
       (SELECT COUNT(*) FROM destinos)   AS destinos,
       (SELECT COUNT(*) FROM categorias) AS categorias,
       (SELECT COUNT(*) FROM imagenes)   AS imagenes,
       (SELECT COUNT(*) FROM usuarios)   AS usuarios,
       (SELECT COUNT(*) FROM favoritos)  AS favoritos,
       (SELECT COUNT(*) FROM recorridos) AS recorridos,
       (SELECT COUNT(*) FROM destinos WHERE latitud IS NULL OR longitud IS NULL) AS sin_coords,
       (SELECT COUNT(*) FROM destinos WHERE verificado = 0) AS sin_verificar,
       (SELECT COUNT(*) FROM destinos d
         WHERE NOT EXISTS (SELECT 1 FROM imagenes i WHERE i.destino_id = d.id)) AS sin_imagen'
)->fetch();

$tarjetas = [
    ['Destinos',      $n['destinos'],   'bi-geo-alt',      '/admin/destinos.php'],
    ['Categorías',    $n['categorias'], 'bi-tags',         '/admin/categorias.php'],
    ['Imágenes',      $n['imagenes'],   'bi-images',       '/admin/destinos.php'],
    ['Usuarios',      $n['usuarios'],   'bi-people',       null],
    ['Favoritos',     $n['favoritos'],  'bi-heart',        null],
    ['Recorridos',    $n['recorridos'], 'bi-signpost-split', null],
];

$pendientes = $pdo->query(
    'SELECT d.id, d.nombre, c.nombre AS categoria,
            (d.latitud IS NULL OR d.longitud IS NULL) AS sin_coords
     FROM destinos d LEFT JOIN categorias c ON c.id = d.categoria_id
     WHERE d.verificado = 0 ORDER BY d.nombre LIMIT 10'
)->fetchAll();

require __DIR__ . '/../includes/header.php';
?>

<div class="container py-5">
  <h1 class="mb-3">Panel administrativo</h1>
  <?php require __DIR__ . '/../includes/admin_menu.php'; ?>

  <div class="row g-3">
    <?php foreach ($tarjetas as [$etiqueta, $valor, $icono, $enlace]): ?>
      <div class="col-6 col-md-4 col-lg-2">
        <<?= $enlace ? 'a href="' . e($enlace) . '"' : 'div' ?> class="stat-card">
          <i class="bi <?= e($icono) ?>"></i>
          <div class="valor"><?= (int) $valor ?></div>
          <div class="etiqueta"><?= e($etiqueta) ?></div>
        </<?= $enlace ? 'a' : 'div' ?>>
      </div>
    <?php endforeach; ?>
  </div>

  <h2 class="seccion-titulo mt-5">Calidad de los datos</h2>
  <div class="row g-3">
    <div class="col-md-4"><div class="stat-card"><i class="bi bi-patch-question"></i>
      <div class="valor"><?= (int) $n['sin_verificar'] ?></div><div class="etiqueta">destinos sin verificar</div></div></div>
    <div class="col-md-4"><div class="stat-card"><i class="bi bi-pin-map"></i>
      <div class="valor"><?= (int) $n['sin_coords'] ?></div><div class="etiqueta">destinos sin coordenadas</div></div></div>
    <div class="col-md-4"><div class="stat-card"><i class="bi bi-image"></i>
      <div class="valor"><?= (int) $n['sin_imagen'] ?></div><div class="etiqueta">destinos sin fotografía</div></div></div>
  </div>

  <h2 class="seccion-titulo mt-5">Pendientes de verificar</h2>
  <?php if ($pendientes): ?>
    <div class="tabla-admin table-responsive">
      <table class="table align-middle mb-0">
        <thead><tr><th>Destino</th><th>Categoría</th><th>Coordenadas</th><th></th></tr></thead>
        <tbody>
          <?php foreach ($pendientes as $p): ?>
            <tr>
              <td><?= e($p['nombre']) ?></td>
              <td><?= e($p['categoria'] ?? 'Sin categoría') ?></td>
              <td><?= $p['sin_coords'] ? '<span class="badge text-bg-warning">Faltan</span>' : '<span class="badge text-bg-success">Cargadas</span>' ?></td>
              <td class="text-end"><a class="btn btn-sm btn-verde" href="/admin/destino_form.php?id=<?= (int) $p['id'] ?>">Editar</a></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php else: ?>
    <div class="vacio"><i class="bi bi-patch-check"></i><p class="mb-0">Todos los destinos están verificados.</p></div>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>