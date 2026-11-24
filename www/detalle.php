<?php
require __DIR__ . '/includes/conexion.php';
require_once __DIR__ . '/includes/funciones.php';

$activo = 'destinos';
$id = (int) ($_GET['id'] ?? 0);

$consulta = $pdo->prepare(
    'SELECT d.*, c.nombre AS categoria
     FROM destinos d LEFT JOIN categorias c ON c.id = d.categoria_id
     WHERE d.id = ?'
);
$consulta->execute([$id]);
$d = $consulta->fetch();

// Destino inexistente -> 404 (se manda ANTES de imprimir HTML)
if (!$d) {
    http_response_code(404);
    $titulo = 'No encontrado';
    require __DIR__ . '/includes/header.php';
    echo '<div class="container py-5"><div class="vacio"><i class="bi bi-emoji-frown"></i>';
    echo '<h3 class="mt-2">Ese destino no existe</h3><a class="btn btn-dorado" href="/destinos.php">Volver al catálogo</a></div></div>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$titulo = $d['nombre'];

// Fotos: solo las que existen como archivo
$imgs  = $pdo->prepare('SELECT url, descripcion FROM imagenes WHERE destino_id = ? ORDER BY id');
$imgs->execute([$id]);
$fotos = [];
foreach ($imgs->fetchAll() as $f) {
    $url = foto_url($f['url']);
    if ($url) {
        $fotos[] = ['url' => $url, 'descripcion' => $f['descripcion']];
    }
}

// Mapa solo si hay coordenadas
$tiene_mapa = $d['latitud'] !== null && $d['longitud'] !== null;
$extra_head = '';
$extra_scripts = '';
if ($tiene_mapa) {
    $extra_head = '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css">';
    ob_start(); ?>
<script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
  const punto = [<?= json_encode((float) $d['latitud']) ?>, <?= json_encode((float) $d['longitud']) ?>];
  const mapa = L.map('mapa').setView(punto, 15);
  L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
  }).addTo(mapa);
  const nombre = document.createElement('strong');
  nombre.textContent = <?= json_encode($d['nombre'], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
  L.marker(punto).addTo(mapa).bindPopup(nombre);
</script>
<?php
    $extra_scripts = ob_get_clean();
}

require __DIR__ . '/includes/header.php';
?>

<div class="container py-5">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="/index.php">Inicio</a></li>
      <li class="breadcrumb-item"><a href="/destinos.php">Destinos</a></li>
      <li class="breadcrumb-item active" aria-current="page"><?= e($d['nombre']) ?></li>
    </ol>
  </nav>

  <div class="row g-4">
    <div class="col-lg-7">
      <?php if ($fotos): ?>
        <div id="galeria" class="carousel slide galeria">
          <div class="carousel-inner rounded-4 shadow">
            <?php foreach ($fotos as $i => $f): ?>
              <div class="carousel-item<?= $i === 0 ? ' active' : '' ?>">
                <img src="<?= e($f['url']) ?>" class="d-block w-100 foto-grande" alt="<?= e($f['descripcion'] ?? $d['nombre']) ?>">
              </div>
            <?php endforeach; ?>
          </div>
          <?php if (count($fotos) > 1): ?>
            <button class="carousel-control-prev" type="button" data-bs-target="#galeria" data-bs-slide="prev">
              <span class="carousel-control-prev-icon" aria-hidden="true"></span><span class="visually-hidden">Anterior</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#galeria" data-bs-slide="next">
              <span class="carousel-control-next-icon" aria-hidden="true"></span><span class="visually-hidden">Siguiente</span>
            </button>
          <?php endif; ?>
        </div>
      <?php else: ?>
        <div class="galeria-vacia">
          <div class="text-center"><i class="bi bi-images fs-1"></i><p class="mb-0">Fotografías pendientes</p></div>
        </div>
      <?php endif; ?>
    </div>

    <div class="col-lg-5">
      <span class="chip-categoria"><?= e($d['categoria'] ?? 'Sin categoría') ?></span>
      <h1 class="mt-2"><?= e($d['nombre']) ?></h1>
      <p class="text-muted"><i class="bi bi-geo-alt"></i> <?= e(dato($d['ubicacion'], 'Ubicación por verificar')) ?></p>
      <p><?= nl2br(e(dato($d['descripcion'], 'Descripción por verificar'))) ?></p>

      <?php if (!$d['verificado']): ?>
        <div class="alert alert-warning small"><i class="bi bi-info-circle"></i> La información de este destino está en proceso de verificación.</div>
      <?php endif; ?>

            <?php $es_fav = in_array((int) $d['id'], favoritos_ids($pdo), true); ?>
      <?php if (esta_logueado()): ?>
        <form method="post" action="/favorito.php" class="d-inline">
          <?= csrf_campo() ?>
          <input type="hidden" name="destino_id" value="<?= (int) $d['id'] ?>">
          <input type="hidden" name="accion" value="<?= $es_fav ? 'quitar' : 'agregar' ?>">
          <input type="hidden" name="volver" value="<?= e($_SERVER['REQUEST_URI'] ?? '/destinos.php') ?>">
          <button type="submit" class="btn <?= $es_fav ? 'btn-danger' : 'btn-outline-danger' ?>">
            <i class="bi <?= $es_fav ? 'bi-heart-fill' : 'bi-heart' ?>"></i>
            <?= $es_fav ? 'Quitar de favoritos' : 'Guardar en favoritos' ?>
          </button>
        </form>
      <?php else: ?>
        <a class="btn btn-outline-danger" href="/login.php"><i class="bi bi-heart"></i> Inicia sesión para guardar</a>
      <?php endif; ?>
    </div>
  </div>

  <h2 class="seccion-titulo mt-5">Ubicación</h2>
  <?php if ($tiene_mapa): ?>
    <div id="mapa" class="mapa"></div>
  <?php else: ?>
    <div class="vacio"><i class="bi bi-map"></i><p class="mb-0">La ubicación exacta está pendiente de verificar.</p></div>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>