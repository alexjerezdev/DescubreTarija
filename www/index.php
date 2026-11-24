<?php
require __DIR__ . '/includes/conexion.php';
$titulo = 'Prueba de estilos';
$activo = 'inicio';
$total  = $pdo->query('SELECT COUNT(*) FROM destinos')->fetchColumn();
$ejemplo = $pdo->query('SELECT d.nombre, c.nombre AS categoria
                        FROM destinos d LEFT JOIN categorias c ON c.id = d.categoria_id
                        ORDER BY d.id LIMIT 3')->fetchAll();
require __DIR__ . '/includes/header.php';
?>

<section class="hero">
  <div class="container">
    <h1>Descubre Tarija</h1>
    <p>Naturaleza, cultura, gastronomía y tradiciones en un solo lugar.</p>
    <a href="#" class="btn btn-dorado btn-lg">Explorar destinos</a>
  </div>
</section>

<div class="container py-5">
  <h2 class="seccion-titulo">Prueba de componentes</h2>
  <p>Conexión a la base de datos: <b><?= e($total) ?></b> destinos encontrados.</p>

  <div class="row g-4">
    <?php foreach ($ejemplo as $d): ?>
      <div class="col-12 col-md-6 col-lg-4">
        <div class="card tarjeta-destino">
          <div class="foto"><i class="bi bi-image fs-1"></i></div>
          <div class="card-body">
            <span class="chip-categoria"><?= e($d['categoria'] ?? 'Sin categoría') ?></span>
            <h3 class="card-title mt-2"><?= e($d['nombre']) ?></h3>
            <p class="small text-muted">Tarija, Bolivia</p>
            <a href="#" class="btn btn-verde btn-sm">Ver detalles</a>
            <button class="btn btn-outline-danger btn-sm" type="button"><i class="bi bi-heart"></i></button>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>