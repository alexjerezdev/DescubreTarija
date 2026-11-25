<?php
require __DIR__ . '/../includes/conexion.php';
require_once __DIR__ . '/../includes/funciones.php';

requerir_admin();

// ---------- ELIMINAR (POST) ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valido()) {
        http_response_code(400);
        poner_flash('danger', 'Solicitud no válida. Intenta de nuevo.');
        header('Location: /admin/destinos.php');
        exit;
    }

    if (($_POST['accion'] ?? '') === 'eliminar') {
        $id = (int) ($_POST['destino_id'] ?? 0);
        $b  = $pdo->prepare('SELECT nombre FROM destinos WHERE id = ?');
        $b->execute([$id]);
        $nombre = $b->fetchColumn();

        if ($nombre === false) {
            poner_flash('danger', 'Ese destino no existe.');
        } else {
            $u = $pdo->prepare('SELECT url FROM imagenes WHERE destino_id = ?');
            $u->execute([$id]);
            $urls = $u->fetchAll(PDO::FETCH_COLUMN);

            // Primero las tablas hijas, al final el destino. Todo o nada.
            $pdo->beginTransaction();
            try {
                foreach (['recorrido_destinos', 'favoritos', 'imagenes'] as $tabla) {
                    $pdo->prepare("DELETE FROM $tabla WHERE destino_id = ?")->execute([$id]);
                }
                $pdo->prepare('DELETE FROM destinos WHERE id = ?')->execute([$id]);
                $pdo->commit();
            } catch (Throwable $ex) {
                $pdo->rollBack();
                throw $ex;
            }
            foreach ($urls as $url) {
                borrar_archivo_subido($url);
            }
            poner_flash('success', 'Eliminaste «' . $nombre . '».');
        }
    }
    header('Location: /admin/destinos.php');
    exit;
}

$titulo       = 'Admin · Destinos';
$activo       = 'admin';
$admin_activo = 'destinos';

$destinos = $pdo->query(
    'SELECT d.id, d.nombre, d.destacado, d.verificado, d.latitud, d.longitud,
            c.nombre AS categoria,
            (SELECT COUNT(*) FROM imagenes i WHERE i.destino_id = d.id) AS fotos
     FROM destinos d LEFT JOIN categorias c ON c.id = d.categoria_id
     ORDER BY d.nombre'
)->fetchAll();

require __DIR__ . '/../includes/header.php';
?>

<div class="container py-5">
  <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h1 class="mb-0">Destinos</h1>
    <a class="btn btn-dorado" href="/admin/destino_form.php"><i class="bi bi-plus-lg"></i> Nuevo destino</a>
  </div>
  <?php require __DIR__ . '/../includes/admin_menu.php'; ?>

  <div class="tabla-admin table-responsive">
    <table class="table align-middle mb-0">
      <thead>
        <tr><th>Nombre</th><th>Categoría</th><th>Coord.</th><th>Verificado</th><th>Destacado</th><th>Fotos</th><th class="text-end">Acciones</th></tr>
      </thead>
      <tbody>
        <?php foreach ($destinos as $d): ?>
          <tr>
            <td class="fw-semibold"><?= e($d['nombre']) ?></td>
            <td><?= e($d['categoria'] ?? 'Sin categoría') ?></td>
            <td><?= ($d['latitud'] !== null && $d['longitud'] !== null) ? '<i class="bi bi-check-circle-fill text-success"></i>' : '<i class="bi bi-dash-circle text-muted"></i>' ?></td>
            <td><?= $d['verificado'] ? '<span class="badge text-bg-success">Sí</span>' : '<span class="badge text-bg-warning">No</span>' ?></td>
            <td><?= $d['destacado'] ? '<i class="bi bi-star-fill" style="color:var(--dorado)"></i>' : '<i class="bi bi-star text-muted"></i>' ?></td>
            <td><?= (int) $d['fotos'] ?></td>
            <td class="text-end text-nowrap">
              <a class="btn btn-sm btn-outline-secondary" href="/detalle.php?id=<?= (int) $d['id'] ?>" title="Ver en el sitio" aria-label="Ver en el sitio"><i class="bi bi-eye"></i></a>
              <a class="btn btn-sm btn-outline-secondary" href="/admin/imagenes.php?destino_id=<?= (int) $d['id'] ?>" title="Imágenes" aria-label="Imágenes"><i class="bi bi-images"></i></a>
              <a class="btn btn-sm btn-verde" href="/admin/destino_form.php?id=<?= (int) $d['id'] ?>" title="Editar" aria-label="Editar"><i class="bi bi-pencil"></i></a>
              <form method="post" class="d-inline"
                    onsubmit="return confirm('¿Eliminar este destino? Se quitará también de favoritos y recorridos, y se borrarán sus fotos.');">
                <?= csrf_campo() ?>
                <input type="hidden" name="accion" value="eliminar">
                <input type="hidden" name="destino_id" value="<?= (int) $d['id'] ?>">
                <button class="btn btn-sm btn-outline-danger" type="submit" title="Eliminar" aria-label="Eliminar"><i class="bi bi-trash"></i></button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>