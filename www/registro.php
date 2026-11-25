<?php
require __DIR__ . '/includes/conexion.php';
require_once __DIR__ . '/includes/funciones.php';

// Si ya tiene sesión no tiene sentido registrarse otra vez
if (esta_logueado()) {
    header('Location: /index.php');
    exit;
}

$errores = [];
$nombre  = '';
$email   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !csrf_valido()) {
    http_response_code(400);
    $errores[] = 'Solicitud no válida. Recarga la página e intenta de nuevo.';
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1) Recibir y limpiar
    $nombre = trim($_POST['nombre'] ?? '');
    $email  = mb_strtolower(trim($_POST['email'] ?? ''));
    $clave  = $_POST['clave']  ?? '';
    $clave2 = $_POST['clave2'] ?? '';

    // 2) Validar (nunca confiar en lo que llega del navegador)
    if (mb_strlen($nombre) < 2 || mb_strlen($nombre) > 80) {
        $errores[] = 'El nombre debe tener entre 2 y 80 caracteres.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 100) {
        $errores[] = 'El correo no parece válido.';
    }
    if (strlen($clave) < 8) {
        $errores[] = 'La contraseña debe tener al menos 8 caracteres.';
    }
    if (strlen($clave) > 72) {
        $errores[] = 'La contraseña es demasiado larga (máximo 72 caracteres).';
    }
    if ($clave !== $clave2) {
        $errores[] = 'Las contraseñas no coinciden.';
    }

    // 3) Guardar con consulta preparada y hash. El rol NO se envía: la BD pone 'usuario'.
    if (!$errores) {
        try {
            $guardar = $pdo->prepare('INSERT INTO usuarios (nombre, email, password_hash) VALUES (?, ?, ?)');
            $guardar->execute([$nombre, $email, password_hash($clave, PASSWORD_DEFAULT)]);
            poner_flash('success', 'Cuenta creada. Ya puedes iniciar sesión.');
            header('Location: /login.php');
            exit;
        } catch (PDOException $ex) {
            // 1062 = valor duplicado (el correo es UNIQUE)
            if ((int) ($ex->errorInfo[1] ?? 0) === 1062) {
                $errores[] = 'Ese correo ya está registrado.';
            } else {
                throw $ex;
            }
        }
    }
}

$titulo = 'Crear cuenta';
$activo = 'registro';
require __DIR__ . '/includes/header.php';
?>

<div class="container py-5">
  <div class="card-auth">
    <h1 class="mb-3">Crear cuenta</h1>

    <?php if ($errores): ?>
      <div class="alert alert-danger">
        <ul class="mb-0">
          <?php foreach ($errores as $err): ?><li><?= e($err) ?></li><?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="post" novalidate>
            <?= csrf_campo() ?>
      <div class="mb-3">
        <label class="form-label" for="nombre">Nombre</label>
        <input id="nombre" name="nombre" class="form-control" value="<?= e($nombre) ?>" maxlength="80" autocomplete="name" required>
      </div>
      <div class="mb-3">
        <label class="form-label" for="email">Correo</label>
        <input id="email" name="email" type="email" class="form-control" value="<?= e($email) ?>" maxlength="100" autocomplete="email" required>
      </div>
      <div class="mb-3">
        <label class="form-label" for="clave">Contraseña <span class="text-muted small">(mínimo 8)</span></label>
        <input id="clave" name="clave" type="password" class="form-control" autocomplete="new-password" required>
      </div>
      <div class="mb-3">
        <label class="form-label" for="clave2">Repite la contraseña</label>
        <input id="clave2" name="clave2" type="password" class="form-control" autocomplete="new-password" required>
      </div>
      <button class="btn btn-dorado w-100" type="submit">Registrarme</button>
    </form>

    <p class="small text-center mt-3 mb-0">¿Ya tienes cuenta? <a href="/login.php">Inicia sesión</a></p>
  </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>