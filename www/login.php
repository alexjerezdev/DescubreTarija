<?php
require __DIR__ . '/includes/conexion.php';
require_once __DIR__ . '/includes/funciones.php';

if (esta_logueado()) {
    header('Location: /index.php');
    exit;
}

$errores = [];
$email   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $clave = $_POST['clave'] ?? '';

    if ($email === '' || $clave === '') {
        $errores[] = 'Escribe tu correo y tu contraseña.';
    } else {
        $consulta = $pdo->prepare('SELECT id, nombre, password_hash, rol FROM usuarios WHERE email = ?');
        $consulta->execute([$email]);
        $u = $consulta->fetch();

        // password_verify compara la clave escrita contra el hash guardado
        if ($u && password_verify($clave, $u['password_hash'])) {
            session_regenerate_id(true);     // nueva sesión: evita fijación de sesión
            $_SESSION['usuario'] = [
                'id'     => (int) $u['id'],
                'nombre' => $u['nombre'],
                'rol'    => $u['rol'],
            ];
            poner_flash('success', 'Bienvenido, ' . $u['nombre'] . '.');
            header('Location: /index.php');
            exit;
        }
        // Mismo mensaje si el correo no existe o la clave es mala
        $errores[] = 'Correo o contraseña incorrectos.';
    }
}

$titulo = 'Iniciar sesión';
$activo = 'login';
require __DIR__ . '/includes/header.php';
?>

<div class="container py-5">
  <div class="card-auth">
    <h1 class="mb-3">Iniciar sesión</h1>

    <?php if ($errores): ?>
      <div class="alert alert-danger">
        <?php foreach ($errores as $err): ?><div><?= e($err) ?></div><?php endforeach; ?>
      </div>
    <?php endif; ?>

    <form method="post" novalidate>
      <div class="mb-3">
        <label class="form-label" for="email">Correo</label>
        <input id="email" name="email" type="email" class="form-control" value="<?= e($email) ?>" autocomplete="email" required>
      </div>
      <div class="mb-3">
        <label class="form-label" for="clave">Contraseña</label>
        <input id="clave" name="clave" type="password" class="form-control" autocomplete="current-password" required>
      </div>
      <button class="btn btn-dorado w-100" type="submit">Entrar</button>
    </form>

    <p class="small text-center mt-3 mb-0">¿No tienes cuenta? <a href="/registro.php">Regístrate</a></p>
  </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>