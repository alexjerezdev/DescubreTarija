<?php
require __DIR__ . '/includes/conexion.php';
require_once __DIR__ . '/includes/funciones.php';

requerir_login();
$activo = 'recorridos';

// Botón pequeño (formulario POST) para cada paso
function boton_paso(int $recorrido_id, int $destino_id, string $accion, string $icono,
                    string $clase, string $titulo, bool $apagado = false): string
{
    return '<form method="post" action="/recorrido_accion.php" class="d-inline">'
        . csrf_campo()
        . '<input type="hidden" name="accion" value="' . e($accion) . '">'
        . '<input type="hidden" name="recorrido_id" value="' . $recorrido_id . '">'
        . '<input type="hidden" name="destino_id" value="' . $destino_id . '">'
        . '<button type="submit" class="btn btn-sm ' . e($clase) . '" title="' . e($titulo) . '"'
        . ' aria-label="' . e($titulo) . '"' . ($apagado ? ' disabled' : '') . '>'
        . '<i class="bi ' . e($icono) . '"></i></button></form>';
}

$id = (int) ($_GET['id'] ?? 0);
$r  = recorrido_propio($pdo, $id);

// No existe o es de otro usuario: misma respuesta (no revelamos si existe)
if (!$r) {
    http_response_code(404);
    $titulo = 'No encontrado';
    require __DIR__ . '/includes/header.php';
    echo '<div class="container py-5"><div class="vacio"><i class="bi bi-emoji-frown"></i>';
    echo '<h3 class="mt-2">Ese recorrido no existe</h3><a class="btn btn-dorado" href="/recorridos.php">Volver a mis recorridos</a></div></div>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$titulo = $r['nombre'];

$c = $pdo->prepare(
    'SELECT d.id, d.nombre, d.ubicacion, d.latitud, d.longitud, c.nombre AS categoria, rd.orden
     FROM recorrido_destinos rd
     JOIN destinos d ON d.id = rd.destino_id
     LEFT JOIN categorias c ON c.id = d.categoria_id
     WHERE rd.recorrido_id = ?
     ORDER BY rd.orden, rd.id'
);
$c->execute([$r['id']]);
$pasos = $c->fetchAll();
$total = count($pasos);

// Marcadores: solo destinos con coordenadas, con su número de paso
$puntos = [];
foreach ($pasos as $i => $p) {
    if ($p['latitud'] !== null && $p['longitud'] !== null) {
        $puntos[] = [
            'n'         => $i + 1,
            'id'        => (int) $p['id'],
            'nombre'    => $p['nombre'],
            'categoria' => $p['categoria'] ?? 'Sin categoría',
            'lat'       => (float) $p['latitud'],
            'lng'       => (float) $p['longitud'],
        ];
    }
}

$extra_head = '';
$extra_scripts = '';
if ($puntos) {
    $extra_head = '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css">';
    ob_start(); ?>
<script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
  const puntos = <?= json_encode($puntos, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

  const mapa = L.map('mapa').setView([-21.5355, -64.7296], 11);
  L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
  }).addTo(mapa);

  const linea = [];
  puntos.forEach(function (p) {
    const icono = L.divIcon({
      className: '',
      html: '<div class="num-marker">' + String(Number(p.n)) + '</div>',
      iconSize: [30, 30], iconAnchor: [15, 15]
    });
    const caja = document.createElement('div');
    const t = document.createElement('strong'); t.textContent = p.n + '. ' + p.nombre;
    const c = document.createElement('div');    c.className = 'small text-muted'; c.textContent = p.categoria;
    const a = document.createElement('a');      a.href = '/detalle.php?id=' + p.id; a.textContent = 'Ver detalles';
    caja.append(t, c, a);
    L.marker([p.lat, p.lng], { icon: icono }).addTo(mapa).bindPopup(caja);
    linea.push([p.lat, p.lng]);
  });

  if (linea.length > 1) {
    L.polyline(linea, { color: '#c9a24b', weight: 4, dashArray: '8 8' }).addTo(mapa);
  }
  mapa.fitBounds(linea, { padding: [40, 40], maxZoom: 14 });
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
      <li class="breadcrumb-item"><a href="/recorridos.php">Mis recorridos</a></li>
      <li class="breadcrumb-item active" aria-current="page"><?= e($r['nombre']) ?></li>
    </ol>
  </nav>

  <h1><?= e($r['nombre']) ?></h1>
  <p class="text-muted">
    <i class="bi bi-calendar-event"></i> <?= e(fecha_es($r['fecha_visita'])) ?>
    &nbsp;·&nbsp; <i class="bi bi-geo-alt"></i> <?= $total ?> destino(s)
  </p>

  <div class="row g-4">
    <div class="col-lg-5">
      <h2 class="seccion-titulo h4">Orden del recorrido</h2>

      <?php if ($pasos): ?>
        <?php foreach ($pasos as $i => $p): ?>
          <div class="paso-recorrido">
            <div class="numero"><?= $i + 1 ?></div>
            <div class="info">
              <a class="fw-semibold text-decoration-none" href="/detalle.php?id=<?= (int) $p['id'] ?>"><?= e($p['nombre']) ?></a>
              <div class="small text-muted"><?= e($p['categoria'] ?? 'Sin categoría') ?></div>
            </div>
            <div class="acciones">
              <?= boton_paso((int) $r['id'], (int) $p['id'], 'subir', 'bi-arrow-up', 'btn-outline-secondary', 'Subir', $i === 0) ?>
              <?= boton_paso((int) $r['id'], (int) $p['id'], 'bajar', 'bi-arrow-down', 'btn-outline-secondary', 'Bajar', $i === $total - 1) ?>
              <?= boton_paso((int) $r['id'], (int) $p['id'], 'quitar', 'bi-x-lg', 'btn-outline-danger', 'Quitar del recorrido') ?>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="vacio">
          <i class="bi bi-signpost-split"></i>
          <p class="mb-2">Este recorrido aún no tiene destinos.</p>
        </div>
      <?php endif; ?>

      <div class="d-flex flex-wrap gap-2 mt-3">
        <a class="btn btn-dorado" href="/destinos.php"><i class="bi bi-plus-lg"></i> Agregar destinos</a>

        <form method="post" action="/recorrido_accion.php"
              onsubmit="return confirm('¿Eliminar este recorrido? No se puede deshacer.');">
          <?= csrf_campo() ?>
          <input type="hidden" name="accion" value="eliminar">
          <input type="hidden" name="recorrido_id" value="<?= (int) $r['id'] ?>">
          <button type="submit" class="btn btn-outline-danger"><i class="bi bi-trash"></i> Eliminar recorrido</button>
        </form>
      </div>
    </div>

    <div class="col-lg-7">
      <h2 class="seccion-titulo h4">Mapa del recorrido</h2>
      <?php if ($puntos): ?>
        <div id="mapa" class="mapa"></div>
        <p class="small text-muted mt-2">
          <?= count($puntos) ?> de <?= $total ?> destinos tienen ubicación verificada.
          La línea une los puntos en orden; no es una ruta por carreteras.
        </p>
      <?php else: ?>
        <div class="vacio">
          <i class="bi bi-map"></i>
          <p class="mb-0">Ningún destino de este recorrido tiene ubicación verificada todavía.</p>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>