<?php
require __DIR__ . '/../includes/conexion.php';
require_once __DIR__ . '/../includes/funciones.php';

requerir_admin();

$id       = (int) ($_GET['id'] ?? 0);
$editando = $id > 0;
$errores  = [];

// Valores iniciales del formulario
$v = [
    'nombre' => '', 'descripcion' => '', 'ubicacion' => '', 'latitud' => '', 'longitud' => '',
    'categoria_id' => 0, 'destacado' => 0, 'verificado' => 0,
];

if ($editando) {
    $c = $pdo->prepare('SELECT * FROM destinos WHERE id = ?');
    $c->execute([$id]);
    $fila = $c->fetch();
    if (!$fila) {
        poner_flash('danger', 'Ese destino no existe.');
        header('Location: /admin/destinos.php');
        exit;
    }
    $v = [
        'nombre'       => $fila['nombre'],
        'descripcion'  => $fila['descripcion'] ?? '',
        'ubicacion'    => $fila['ubicacion'] ?? '',
        'latitud'      => $fila['latitud'] ?? '',
        'longitud'     => $fila['longitud'] ?? '',
        'categoria_id' => (int) ($fila['categoria_id'] ?? 0),
        'destacado'    => (int) $fila['destacado'],
        'verificado'   => (int) $fila['verificado'],
    ];
}

$categorias = $pdo->query('SELECT id, nombre FROM categorias ORDER BY id')->fetchAll();

// ---------- GUARDAR (POST) ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valido()) {
        http_response_code(400);
        $errores[] = 'Solicitud no válida. Recarga la página e intenta de nuevo.';
    } else {
        // 1) Recibir y limpiar
        $v['nombre']       = trim($_POST['nombre'] ?? '');
        $v['descripcion']  = trim($_POST['descripcion'] ?? '');
        $v['ubicacion']    = trim($_POST['ubicacion'] ?? '');
        $v['latitud']      = str_replace(',', '.', trim($_POST['latitud'] ?? ''));
        $v['longitud']     = str_replace(',', '.', trim($_POST['longitud'] ?? ''));
        $v['categoria_id'] = (int) ($_POST['categoria_id'] ?? 0);      // 0 = sin categoría
        $v['destacado']    = isset($_POST['destacado'])  ? 1 : 0;
        $v['verificado']   = isset($_POST['verificado']) ? 1 : 0;

        // 2) Validar
        if (mb_strlen($v['nombre']) < 2 || mb_strlen($v['nombre']) > 80) {
            $errores[] = 'El nombre debe tener entre 2 y 80 caracteres.';
        }
        if (mb_strlen($v['descripcion']) > 5000) {
            $errores[] = 'La descripción es demasiado larga (máximo 5000 caracteres).';
        }
        if (mb_strlen($v['ubicacion']) > 100) {
            $errores[] = 'La ubicación admite máximo 100 caracteres.';
        }

        $lat = $v['latitud'];
        $lng = $v['longitud'];
        if (($lat === '') xor ($lng === '')) {
            $errores[] = 'Escribe latitud y longitud juntas, o deja ambas vacías.';
        } elseif ($lat !== '') {
            if (!is_numeric($lat) || $lat < -90 || $lat > 90) {
                $errores[] = 'La latitud debe ser un número entre -90 y 90.';
            }
            if (!is_numeric($lng) || $lng < -180 || $lng > 180) {
                $errores[] = 'La longitud debe ser un número entre -180 y 180.';
            }
        }

        if ($v['categoria_id'] > 0) {
            $x = $pdo->prepare('SELECT 1 FROM categorias WHERE id = ?');
            $x->execute([$v['categoria_id']]);
            if (!$x->fetchColumn()) {
                $errores[] = 'La categoría elegida no existe.';
            }
        }

        // 3) Guardar (campos vacíos opcionales -> NULL)
        if (!$errores) {
            $datos = [
                $v['nombre'],
                $v['descripcion'] !== '' ? $v['descripcion'] : null,
                $v['ubicacion']   !== '' ? $v['ubicacion']   : null,
                $lat !== '' ? round((float) $lat, 6) : null,
                $lng !== '' ? round((float) $lng, 6) : null,
                $v['categoria_id'] > 0 ? $v['categoria_id'] : null,
                $v['destacado'],
                $v['verificado'],
            ];

            try {
                if ($editando) {
                    $pdo->prepare(
                        'UPDATE destinos SET nombre = ?, descripcion = ?, ubicacion = ?, latitud = ?, longitud = ?,
                                categoria_id = ?, destacado = ?, verificado = ? WHERE id = ?'
                    )->execute([...$datos, $id]);
                    poner_flash('success', 'Destino actualizado.');
                    header('Location: /admin/destinos.php');
                } else {
                    $pdo->prepare(
                        'INSERT INTO destinos (nombre, descripcion, ubicacion, latitud, longitud, categoria_id, destacado, verificado)
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
                    )->execute($datos);
                    poner_flash('success', 'Destino creado. Ahora puedes subirle fotografías.');
                    header('Location: /admin/imagenes.php?destino_id=' . (int) $pdo->lastInsertId());
                }
                exit;
            } catch (PDOException $ex) {
                // 1062 = valor duplicado (nombre UNIQUE)
                if ((int) ($ex->errorInfo[1] ?? 0) === 1062) {
                    $errores[] = 'Ya existe un destino con ese nombre.';
                } else {
                    throw $ex;
                }
            }
        }
    }
}

$titulo       = $editando ? 'Editar destino' : 'Nuevo destino';
$activo       = 'admin';
$admin_activo = 'destinos';
require __DIR__ . '/../includes/header.php';
?>

<div class="container py-5">
  <h1 class="mb-3"><?= $editando ? 'Editar destino' : 'Nuevo destino' ?></h1>
  <?php require __DIR__ . '/../includes/admin_menu.php'; ?>

  <?php if ($errores): ?>
    <div class="alert alert-danger">
      <ul class="mb-0"><?php foreach ($errores as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul>
    </div>
  <?php endif; ?>

  <div class="alert alert-warning small">
    <i class="bi bi-info-circle"></i> No inventes datos. Contrasta descripción y coordenadas con una fuente confiable
    (por ejemplo OpenStreetMap y un sitio oficial) y marca «verificado» solo si lo hiciste.
  </div>

  <form method="post" novalidate>
    <?= csrf_campo() ?>
    <div class="row g-3">
      <div class="col-md-8">
        <label class="form-label" for="nombre">Nombre *</label>
        <input id="nombre" name="nombre" class="form-control" maxlength="80" value="<?= e($v['nombre']) ?>" required>
      </div>
      <div class="col-md-4">
        <label class="form-label" for="categoria_id">Categoría</label>
        <select id="categoria_id" name="categoria_id" class="form-select">
          <option value="0">Sin categoría</option>
          <?php foreach ($categorias as $c): ?>
            <option value="<?= (int) $c['id'] ?>"<?= $v['categoria_id'] === (int) $c['id'] ? ' selected' : '' ?>><?= e($c['nombre']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="col-12">
        <label class="form-label" for="ubicacion">Ubicación <span class="text-muted small">(municipio o zona)</span></label>
        <input id="ubicacion" name="ubicacion" class="form-control" maxlength="100" value="<?= e($v['ubicacion']) ?>">
      </div>

      <div class="col-12">
        <label class="form-label" for="descripcion">Descripción</label>
        <textarea id="descripcion" name="descripcion" class="form-control" rows="6"><?= e($v['descripcion']) ?></textarea>
      </div>

      <div class="col-md-6">
        <label class="form-label" for="latitud">Latitud <span class="text-muted small">(ej: -21.xxxxxx)</span></label>
        <input id="latitud" name="latitud" class="form-control" inputmode="decimal" value="<?= e($v['latitud']) ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label" for="longitud">Longitud <span class="text-muted small">(ej: -64.xxxxxx)</span></label>
        <input id="longitud" name="longitud" class="form-control" inputmode="decimal" value="<?= e($v['longitud']) ?>">
      </div>

      <div class="col-12 d-flex flex-wrap gap-4">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" id="destacado" name="destacado" value="1"<?= $v['destacado'] ? ' checked' : '' ?>>
          <label class="form-check-label" for="destacado">Destacado (aparece en la portada)</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" id="verificado" name="verificado" value="1"<?= $v['verificado'] ? ' checked' : '' ?>>
          <label class="form-check-label" for="verificado">Información verificada</label>
        </div>
      </div>
    </div>

    <div class="d-flex flex-wrap gap-2 mt-4">
      <button class="btn btn-dorado" type="submit"><i class="bi bi-check-lg"></i> Guardar</button>
      <a class="btn btn-outline-secondary" href="/admin/destinos.php">Cancelar</a>
      <?php if ($editando): ?>
        <a class="btn btn-outline-secondary ms-auto" href="/admin/imagenes.php?destino_id=<?= (int) $id ?>"><i class="bi bi-images"></i> Gestionar imágenes</a>
      <?php endif; ?>
    </div>
  </form>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>