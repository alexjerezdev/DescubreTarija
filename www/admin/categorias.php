<?php
require __DIR__ . '/../includes/conexion.php';
require_once __DIR__ . '/../includes/funciones.php';

requerir_admin();

// ---------- ACCIONES (POST) ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valido()) {
        http_response_code(400);
        poner_flash('danger', 'Solicitud no válida. Intenta de nuevo.');
        header('Location: /admin/categorias.php');
        exit;
    }

    $accion = $_POST['accion'] ?? '';
    $id     = (int) ($_POST['id'] ?? 0);
    $volver = '/admin/categorias.php';

    if ($accion === 'crear' || $accion === 'editar') {
        $nombre = trim($_POST['nombre'] ?? '');
        $desc   = trim($_POST['descripcion'] ?? '');
        $error  = '';

        if (mb_strlen($nombre) < 2 || mb_strlen($nombre) > 40) {
            $error = 'El nombre debe tener entre 2 y 40 caracteres.';
        } elseif (mb_strlen($desc) > 200) {
            $error = 'La descripción admite máximo 200 caracteres.';
        }

        if ($error === '') {
            try {
                if ($accion === 'crear') {
                    $pdo->prepare('INSERT INTO categorias (nombre, descripcion) VALUES (?, ?)')
                        ->execute([$nombre, $desc !== '' ? $desc : null]);
                    poner_flash('success', 'Categoría creada.');
                } else {
                    $pdo->prepare('UPDATE categorias SET nombre = ?, descripcion = ? WHERE id = ?')
                        ->execute([$nombre, $desc !== '' ? $desc : null, $id]);
                    poner_flash('success', 'Categoría actualizada.');
                }
            } catch (PDOException $ex) {
                if ((int) ($ex->errorInfo[1] ?? 0) === 1062) {
                    $error = 'Ya existe una categoría con ese nombre.';
                } else {
                    throw $ex;
                }
            }
        }
        if ($error !== '') {
            poner_flash('danger', $error);
            if ($accion === 'editar') {
                $volver = '/admin/categorias.php?editar=' . $id;
            }
        }
    }

    if ($accion === 'eliminar') {
        $t = $pdo->prepare('SELECT COUNT(*) FROM destinos WHERE categoria_id = ?');
        $t->execute([$id]);
        if ((int) $t->fetchColumn() > 0) {
            poner_flash('danger', 'No se puede eliminar: la categoría tiene destinos. Reasígnalos primero.');
        } else {
            $pdo->prepare('DELETE FROM categorias WHERE id = ?')->execute([$id]);
            poner_flash('info', 'Categoría eliminada.');
        }
    }

    header('Location: ' . $volver);
    exit;
}

$titulo       = 'Admin · Categorías';
$activo       = 'admin';
$admin_activo = 'categorias';

// Modo edición
$edit = null;
if ((int) ($_GET['editar'] ?? 0) > 0) {
    $q = $pdo->prepare('SELECT id, nombre, descripcion FROM categorias WHERE id = ?');
    $q->execute([(int) $_GET['editar']]);
    $edit = $q->fetch() ?: null;
}

$categorias = $pdo->query(
    'SELECT c.id, c.nombre, c.descripcion, COUNT(d.id) AS total
     FROM categorias c LEFT JOIN destinos d ON d.categoria_id = c.id
     GROUP BY c.id, c.nombre, c.descripcion ORDER BY c.id'
)->fetchAll();

require __DIR__ . '/../includes/header.php';
?>

<div class="container py-5">
  <h1 class="mb-3">Categorías</h1>
  <?php require __DIR__ . '/../includes/admin_menu.php'; ?>

  <div class="row g-4">
    <div class="col-lg-4">
      <div class="card-auth" style="max-width:none">
        <h2 class="h5 mb-3"><?= $edit ? 'Editar categoría' : 'Nueva categoría' ?></h2>
        <form method="post">
          <?= csrf_campo() ?>
          <input type="hidden" name="accion" value="<?= $edit ? 'editar' : 'crear' ?>">
          <?php if ($edit): ?><input type="hidden" name="id" value="<?= (int) $edit['id'] ?>"><?php endif; ?>
          <div class="mb-3">
            <label class="form-label" for="nombre">Nombre *</label>
            <input id="nombre" name="nombre" class="form-control" maxlength="40" required value="<?= e($edit['nombre'] ?? '') ?>">
          </div>
          <div class="mb-3">
            <label class="form-label" for="descripcion">Descripción</label>
            <input id="descripcion" name="descripcion" class="form-control" maxlength="200" value="<?= e($edit['descripcion'] ?? '') ?>">
          </div>
          <button class="btn btn-dorado w-100" type="submit"><i class="bi bi-check-lg"></i> <?= $edit ? 'Guardar cambios' : 'Crear categoría' ?></button>
          <?php if ($edit): ?>
            <a class="btn btn-outline-secondary w-100 mt-2" href="/admin/categorias.php">Cancelar</a>
          <?php endif; ?>
        </form>
      </div>
    </div>

    <div class="col-lg-8">
      <div class="tabla-admin table-responsive">
        <table class="table align-middle mb-0">
          <thead><tr><th>Nombre</th><th>Descripción</th><th>Destinos</th><th class="text-end">Acciones</th></tr></thead>
          <tbody>
            <?php foreach ($categorias as $c): ?>
              <tr>
                <td class="fw-semibold"><?= e($c['nombre']) ?></td>
                <td class="small"><?= e($c['descripcion'] ?? '—') ?></td>
                <td><?= (int) $c['total'] ?></td>
                <td class="text-end text-nowrap">
                  <a class="btn btn-sm btn-verde" href="/admin/categorias.php?editar=<?= (int) $c['id'] ?>" title="Editar" aria-label="Editar"><i class="bi bi-pencil"></i></a>
                  <form method="post" class="d-inline" onsubmit="return confirm('¿Eliminar esta categoría?');">
                    <?= csrf_campo() ?>
                    <input type="hidden" name="accion" value="eliminar">
                    <input type="hidden" name="id" value="<?= (int) $c['id'] ?>">
                    <button class="btn btn-sm btn-outline-danger" type="submit"
                            title="<?= $c['total'] > 0 ? 'Tiene destinos: no se puede eliminar' : 'Eliminar' ?>"
                            aria-label="Eliminar"<?= $c['total'] > 0 ? ' disabled' : '' ?>><i class="bi bi-trash"></i></button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <p class="small text-muted mt-2">Los íconos de la portada están definidos para las 6 categorías iniciales; una categoría nueva usa un ícono genérico.</p>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>