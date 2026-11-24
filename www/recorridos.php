<?php
require __DIR__ . '/includes/conexion.php';
require_once __DIR__ . '/includes/funciones.php';

requerir_login();

$titulo = 'Mis recorridos';
$activo = 'recorridos';

$consulta = $pdo->prepare(
    'SELECT r.id, r.nombre, r.fecha_visita, COUNT(rd.id) AS total
     FROM recorridos r
     LEFT JOIN recorrido_destinos rd ON rd.recorrido_id = r.id
     WHERE r.usuario_id = ?
     GROUP BY r.id, r.nombre, r.fecha_visita
     ORDER BY r.id DESC'
);
$consulta->execute([usuario_actual()['id']]);
$recorridos = $consulta->fetchAll();

require __DIR__ . '/includes/header.php';
?>

<div class="container py-5">
  <h1 class="mb-4">Mis recorridos</h1>

  <div class="row g-4">
    <div class="col-lg-4">
      <div class="card-auth" style="max-width:none">
        <h2 class="h4 mb-3">Nuevo recorrido</h2>
        <form method="post" action="/recorrido_accion.php">
          <?= csrf_campo() ?>
          <input type="hidden" name="accion" value="crear">
          <div class="mb-3">
            <label class="form-label" for="nombre">Nombre</label>
            <input id="nombre" name="nombre" class="form-control" maxlength="80" required placeholder="Ej: Fin de semana en Tarija">
          </div>
          <div class="mb-3">
            <label class="form-label" for="fecha_visita">Fecha de visita <span class="text-muted small">(opcional)</span></label>
            <input id="fecha_visita" name="fecha_visita" type="date" class="form-control">
          </div>
          <button class="btn btn-dorado w-100" type="submit"><i class="bi bi-plus-lg"></i> Crear recorrido</button>
        </form>
      </div>
    </div>

    <div class="col-lg-8">
      <?php if ($recorridos): ?>
        <?php foreach ($recorridos as $r): ?>
          <div class="tarjeta-recorrido d-flex align-items-center gap-3">
            <div class="flex-grow-1">
              <h3 class="h5 mb-1"><?= e($r['nombre']) ?></h3>
              <div class="small text-muted">
                <i class="bi bi-calendar-event"></i> <?= e(fecha_es($r['fecha_visita'])) ?>
                &nbsp;·&nbsp; <i class="bi bi-geo-alt"></i> <?= (int) $r['total'] ?> destino(s)
              </div>
            </div>
            <a class="btn btn-verde btn-sm" href="/recorrido.php?id=<?= (int) $r['id'] ?>">Abrir</a>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="vacio">
          <i class="bi bi-signpost-split"></i>
          <h3 class="mt-2">Aún no tienes recorridos</h3>
          <p class="mb-0">Crea el primero con el formulario y luego agrégale destinos.</p>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>