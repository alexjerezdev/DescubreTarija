<?php
require __DIR__ . '/includes/conexion.php';
require_once __DIR__ . '/includes/funciones.php';

$titulo = 'Inicio';
$activo = 'inicio';

$categorias = $pdo->query(
    'SELECT c.id, c.nombre, COUNT(d.id) AS total
     FROM categorias c LEFT JOIN destinos d ON d.categoria_id = c.id
     GROUP BY c.id, c.nombre ORDER BY c.id'
)->fetchAll();

$destacados   = $pdo->query(SQL_DESTINOS . ' WHERE d.destacado = 1 ORDER BY d.nombre LIMIT 6')->fetchAll();
$recomendados = $pdo->query(SQL_DESTINOS . ' ORDER BY RAND() LIMIT 3')->fetchAll();

$iconos = [
    'Naturaleza'                 => 'bi-tree',
    'Cultura e historia'         => 'bi-bank',
    'Gastronomía'                => 'bi-cup-hot',
    'Aventura'                   => 'bi-compass',
    'Turismo rural'              => 'bi-house-heart',
    'Festividades y tradiciones' => 'bi-balloon',
];

require __DIR__ . '/includes/header.php';
?>

<section class="hero">
  <div class="container">
    <h1>Descubre Tarija</h1>
    <p>Naturaleza, cultura, gastronomía y tradiciones en un solo lugar.</p>

    <form class="buscador-hero input-group" action="/destinos.php" method="get">
      <input type="search" name="q" class="form-control" placeholder="Busca un destino, ej: Casa Dorada" aria-label="Buscar destino">
      <button class="btn btn-dorado" type="submit"><i class="bi bi-search"></i> Buscar</button>
    </form>

    <a href="/destinos.php" class="btn btn-outline-light mt-2">Explorar destinos</a>
  </div>
</section>

<div class="container py-5">

  <h2 class="seccion-titulo">Categorías turísticas</h2>
  <div class="row g-3">
    <?php foreach ($categorias as $c): ?>
      <div class="col-6 col-md-4 col-lg-2">
        <a class="categoria-box" href="/destinos.php?cat=<?= (int) $c['id'] ?>">
          <i class="bi <?= e($iconos[$c['nombre']] ?? 'bi-geo-alt') ?>"></i>
          <div class="nombre"><?= e($c['nombre']) ?></div>
          <div class="total"><?= (int) $c['total'] ?> destinos</div>
        </a>
      </div>
    <?php endforeach; ?>
  </div>

  <h2 class="seccion-titulo mt-5">Destinos destacados</h2>
  <?php if ($destacados): ?>
    <div class="row g-4">
      <?php foreach ($destacados as $d): ?>
        <?php require __DIR__ . '/includes/tarjeta_destino.php'; ?>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <div class="vacio"><i class="bi bi-stars"></i><p class="mb-0">Aún no hay destinos destacados.</p></div>
  <?php endif; ?>

  <h2 class="seccion-titulo mt-5">Te recomendamos descubrir</h2>
  <div class="row g-4">
    <?php foreach ($recomendados as $d): ?>
      <?php require __DIR__ . '/includes/tarjeta_destino.php'; ?>
    <?php endforeach; ?>
  </div>

</div>

<?php require __DIR__ . '/includes/footer.php'; ?>