<?php
require __DIR__ . '/includes/conexion.php';
require_once __DIR__ . '/includes/funciones.php';

$titulo = 'Mapa turístico';
$activo = 'mapa';

$filas = $pdo->query(
    'SELECT d.id, d.nombre, d.descripcion, d.latitud, d.longitud, c.nombre AS categoria
     FROM destinos d LEFT JOIN categorias c ON c.id = d.categoria_id
     WHERE d.latitud IS NOT NULL AND d.longitud IS NOT NULL'
)->fetchAll();
$total = (int) $pdo->query('SELECT COUNT(*) FROM destinos')->fetchColumn();

$marcadores = array_map(fn($p) => [
    'id'        => (int) $p['id'],
    'nombre'    => $p['nombre'],
    'categoria' => $p['categoria'] ?? 'Sin categoría',
    'resumen'   => mb_strimwidth(dato($p['descripcion'], ''), 0, 100, '…'),
    'lat'       => (float) $p['latitud'],
    'lng'       => (float) $p['longitud'],
], $filas);

$extra_head = '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css">';
ob_start(); ?>
<script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
  const marcadores = <?= json_encode($marcadores, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

  // Vista inicial aproximada de la ciudad de Tarija (verificar). Si hay marcadores, se ajusta a ellos.
  const mapa = L.map('mapa').setView([-21.5355, -64.7296], 11);
  L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
  }).addTo(mapa);

  const puntos = [];
  marcadores.forEach(function (m) {
    const caja = document.createElement('div');
    const t = document.createElement('strong');  t.textContent = m.nombre;
    const c = document.createElement('div');     c.className = 'small text-muted'; c.textContent = m.categoria;
    const r = document.createElement('div');     r.className = 'small mb-1'; r.textContent = m.resumen;
    const a = document.createElement('a');       a.href = '/detalle.php?id=' + m.id; a.textContent = 'Ver detalles';
    caja.append(t, c, r, a);
    L.marker([m.lat, m.lng]).addTo(mapa).bindPopup(caja);
    puntos.push([m.lat, m.lng]);
  });
  if (puntos.length) { mapa.fitBounds(puntos, { padding: [40, 40], maxZoom: 14 }); }
</script>
<?php
$extra_scripts = ob_get_clean();

require __DIR__ . '/includes/header.php';
?>

<div class="container py-5">
  <h1 class="mb-2">Mapa turístico</h1>
  <p class="text-muted">
    <?= count($marcadores) ?> de <?= $total ?> destinos tienen ubicación verificada en el mapa.
  </p>

  <?php if (!$marcadores): ?>
    <div class="alert alert-info"><i class="bi bi-info-circle"></i>
      Aún no hay destinos con coordenadas verificadas. Se irán agregando.</div>
  <?php endif; ?>

  <div id="mapa" class="mapa mapa-grande"></div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>