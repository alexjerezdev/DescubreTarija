<?php
require __DIR__ . '/includes/conexion.php';
require_once __DIR__ . '/includes/funciones.php';

$titulo = 'Destinos';
$activo = 'destinos';

// Filtros (vienen por GET desde el formulario o desde la portada)
$q    = trim($_GET['q']    ?? '');
$cat  = (int) ($_GET['cat'] ?? 0);
$ubic = trim($_GET['ubic'] ?? '');

$donde  = [];
$params = [];
if ($q !== '')    { $donde[] = 'd.nombre LIKE ?';     $params[] = "%$q%"; }
if ($cat > 0)     { $donde[] = 'd.categoria_id = ?';  $params[] = $cat; }
if ($ubic !== '') { $donde[] = 'd.ubicacion LIKE ?';  $params[] = "%$ubic%"; }

$sql = SQL_DESTINOS . ($donde ? ' WHERE ' . implode(' AND ', $donde) : '') . ' ORDER BY d.nombre';
$consulta = $pdo->prepare($sql);
$consulta->execute($params);
$destinos = $consulta->fetchAll();

$categorias = $pdo->query('SELECT id, nombre FROM categorias ORDER BY id')->fetchAll();
$hay_filtros = ($q !== '' || $cat > 0 || $ubic !== '');

require __DIR__ . '/includes/header.php';
?>

<div class="container py-5">
  <h1 class="mb-4">Destinos turísticos</h1>

  <form class="filtros row g-2 align-items-end mb-4" method="get" action="/destinos.php">
    <div class="col-12 col-md-4">
      <label class="form-label small mb-1" for="q">Nombre</label>
      <input id="q" type="search" name="q" class="form-control" value="<?= e($q) ?>" placeholder="Ej: Coimata">
    </div>
    <div class="col-12 col-md-3">
      <label class="form-label small mb-1" for="cat">Categoría</label>
      <select id="cat" name="cat" class="form-select">
        <option value="0">Todas</option>
        <?php foreach ($categorias as $c): ?>
          <option value="<?= (int) $c['id'] ?>"<?= $cat === (int) $c['id'] ? ' selected' : '' ?>><?= e($c['nombre']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-12 col-md-3">
      <label class="form-label small mb-1" for="ubic">Ubicación</label>
      <input id="ubic" type="text" name="ubic" class="form-control" value="<?= e($ubic) ?>" placeholder="Municipio o zona">
    </div>
    <div class="col-12 col-md-2 d-flex gap-2">
      <button class="btn btn-verde flex-grow-1" type="submit"><i class="bi bi-search"></i></button>
      <?php if ($hay_filtros): ?>
        <a class="btn btn-outline-secondary" href="/destinos.php" title="Limpiar filtros"><i class="bi bi-x-lg"></i></a>
      <?php endif; ?>
    </div>
  </form>

  <p class="text-muted"><b><?= count($destinos) ?></b> destino(s) encontrado(s)</p>

  <?php if ($destinos): ?>
    <div class="row g-4">
      <?php foreach ($destinos as $d): ?>
        <?php require __DIR__ . '/includes/tarjeta_destino.php'; ?>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <div class="vacio">
      <i class="bi bi-search"></i>
      <h3 class="mt-2">No encontramos destinos</h3>
      <p>Prueba con otro nombre o quita algún filtro.</p>
      <a class="btn btn-dorado" href="/destinos.php">Ver todos</a>
    </div>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>