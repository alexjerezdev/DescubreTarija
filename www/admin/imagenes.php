<?php
require __DIR__ . '/../includes/conexion.php';
require_once __DIR__ . '/../includes/funciones.php';

requerir_admin();

$destino_id = (int) ($_POST['destino_id'] ?? $_GET['destino_id'] ?? 0);
$b = $pdo->prepare('SELECT id, nombre FROM destinos WHERE id = ?');
$b->execute([$destino_id]);
$destino = $b->fetch();

if (!$destino) {
    poner_flash('danger', 'Ese destino no existe.');
    header('Location: /admin/destinos.php');
    exit;
}
$volver = '/admin/imagenes.php?destino_id=' . (int) $destino['id'];

// ---------- ACCIONES (POST) ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valido()) {
        http_response_code(400);
        poner_flash('danger', 'Solicitud no válida. Intenta de nuevo.');
        header('Location: ' . $volver);
        exit;
    }

    $accion = $_POST['accion'] ?? '';

    // ----- SUBIR -----
    if ($accion === 'subir') {
        $permitidos = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        $max   = 2 * 1024 * 1024;                       // 2 MB
        $f     = $_FILES['foto'] ?? null;
        $desc  = trim($_POST['descripcion'] ?? '');
        $error = '';

        if (mb_strlen($desc) > 100) {
            $error = 'La descripción admite máximo 100 caracteres.';
        } elseif (!$f || $f['error'] === UPLOAD_ERR_NO_FILE) {
            $error = 'Elige una imagen.';
        } elseif ($f['error'] !== UPLOAD_ERR_OK) {
            $error = 'No se pudo subir la imagen (máximo 2 MB).';
        } elseif ($f['size'] > $max) {
            $error = 'La imagen pesa más de 2 MB.';
        } else {
            // Se mira el CONTENIDO real del archivo, no la extensión que escribió el usuario
            $mime = (new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);
            if (!isset($permitidos[$mime]) || @getimagesize($f['tmp_name']) === false) {
                $error = 'Solo se aceptan imágenes JPG, PNG o WebP.';
            } else {
                $carpeta = __DIR__ . '/../assets/images/destinos';
                if (!is_dir($carpeta)) {
                    @mkdir($carpeta, 0775, true);
                }
                // El nombre lo genera el servidor
                $nombre = 'd' . $destino['id'] . '-' . bin2hex(random_bytes(6)) . '.' . $permitidos[$mime];
                if (!move_uploaded_file($f['tmp_name'], $carpeta . '/' . $nombre)) {
                    $error = 'No se pudo guardar el archivo en el servidor.';
                } else {
                    $pdo->prepare('INSERT INTO imagenes (destino_id, url, descripcion) VALUES (?, ?, ?)')
                        ->execute([$destino['id'], 'assets/images/destinos/' . $nombre, $desc !== '' ? $desc : null]);
                }
            }
        }

        if ($error !== '') {
            poner_flash('danger', $error);
        } else {
            poner_flash('success', 'Imagen agregada.');
        }
    }

    // ----- ELIMINAR -----
    if ($accion === 'eliminar') {
        $q = $pdo->prepare('SELECT id, url FROM imagenes WHERE id = ? AND destino_id = ?');
        $q->execute([(int) ($_POST['imagen_id'] ?? 0), $destino['id']]);
        $fila = $q->fetch();
        if ($fila) {
            $pdo->prepare('DELETE FROM imagenes WHERE id = ?')->execute([$fila['id']]);
            borrar_archivo_subido($fila['url']);
            poner_flash('info', 'Imagen eliminada.');
        } else {
            poner_flash('danger', 'Esa imagen no existe.');
        }
    }

    header('Location: ' . $volver);
    exit;
}

$titulo       = 'Imágenes · ' . $destino['nombre'];
$activo       = 'admin';
$admin_activo = 'destinos';

$li = $pdo->prepare('SELECT id, url, descripcion FROM imagenes WHERE destino_id = ? ORDER BY id');
$li->execute([$destino['id']]);
$imagenes = $li->fetchAll();

require __DIR__ . '/../includes/header.php';
?>

<div class="container py-5">
  <h1 class="mb-1">Imágenes</h1>
  <p class="text-muted"><?= e($destino['nombre']) ?></p>
  <?php require __DIR__ . '/../includes/admin_menu.php'; ?>

  <div class="card-auth mb-4" style="max-width:none">
    <h2 class="h5 mb-3">Subir una imagen</h2>
    <form method="post" enctype="multipart/form-data" class="row g-3 align-items-end">
      <?= csrf_campo() ?>
      <input type="hidden" name="accion" value="subir">
      <input type="hidden" name="destino_id" value="<?= (int) $destino['id'] ?>">
      <div class="col-md-5">
        <label class="form-label" for="foto">Archivo <span class="text-muted small">(JPG, PNG o WebP, máx. 2 MB)</span></label>
        <input id="foto" name="foto" type="file" class="form-control" accept="image/jpeg,image/png,image/webp" required>
      </div>
      <div class="col-md-5">
        <label class="form-label" for="descripcion">Descripción <span class="text-muted small">(opcional)</span></label>
        <input id="descripcion" name="descripcion" class="form-control" maxlength="100" placeholder="Ej: Fachada principal">
      </div>
      <div class="col-md-2">
        <button class="btn btn-dorado w-100" type="submit"><i class="bi bi-upload"></i> Subir</button>
      </div>
    </form>
    <p class="small text-muted mt-3 mb-0">Usa fotos propias o con licencia libre. La primera de la lista es la que se ve en las tarjetas.</p>
  </div>

  <?php if ($imagenes): ?>
    <div class="tabla-admin table-responsive">
      <table class="table align-middle mb-0">
        <thead><tr><th>Vista</th><th>Descripción</th><th>Ruta</th><th></th></tr></thead>
        <tbody>
          <?php foreach ($imagenes as $img): ?>
            <?php $url = foto_url($img['url']); ?>
            <tr>
              <td>
                <?php if ($url): ?>
                  <img class="miniatura" src="<?= e($url) ?>" alt="<?= e($img['descripcion'] ?? $destino['nombre']) ?>">
                <?php else: ?>
                  <div class="miniatura-vacia" title="El archivo no existe en el servidor"><i class="bi bi-image"></i></div>
                <?php endif; ?>
              </td>
              <td><?= e($img['descripcion'] ?? '—') ?></td>
              <td class="small text-muted"><?= e($img['url']) ?><?= $url ? '' : ' <span class="badge text-bg-warning">sin archivo</span>' ?></td>
              <td class="text-end">
                <form method="post" onsubmit="return confirm('¿Eliminar esta imagen?');">
                  <?= csrf_campo() ?>
                  <input type="hidden" name="accion" value="eliminar">
                  <input type="hidden" name="destino_id" value="<?= (int) $destino['id'] ?>">
                  <input type="hidden" name="imagen_id" value="<?= (int) $img['id'] ?>">
                  <button class="btn btn-sm btn-outline-danger" type="submit" title="Eliminar" aria-label="Eliminar"><i class="bi bi-trash"></i></button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php else: ?>
    <div class="vacio"><i class="bi bi-images"></i><p class="mb-0">Este destino aún no tiene imágenes.</p></div>
  <?php endif; ?>

  <div class="mt-4">
    <a class="btn btn-outline-secondary" href="/admin/destinos.php"><i class="bi bi-arrow-left"></i> Volver a destinos</a>
  </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>