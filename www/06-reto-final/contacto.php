<?php
// =====================================================================
//  JEFE FINAL · Formulario de contacto para tu proyecto FORJA WEB
//  Este archivo junta TODO lo de hoy: variables, if, arrays, POST,
//  PDO con consultas preparadas... y el escudo htmlspecialchars.
//
//  PARA LLEVARLO A TU PROYECTO:
//   1. Copiá este archivo y conexion.php (de 04-recordar) a tu proyecto.
//   2. Creá la tabla "contactos" en tu base (está en db/init.sql).
//   3. Cambiá los textos y colores a tu gusto.
// =====================================================================

require __DIR__ . '/../04-recordar/conexion.php';

function e($t) { return htmlspecialchars((string) $t, ENT_QUOTES, 'UTF-8'); }

$errores = [];
$datos   = ['nombre' => '', 'correo' => '', 'mensaje' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1) Recibir y limpiar espacios
    foreach ($datos as $campo => $valor) {
        $datos[$campo] = trim($_POST[$campo] ?? '');
    }

    // 2) Validar: nunca confíes en lo que llega del navegador
    if (mb_strlen($datos['nombre']) < 2) {
        $errores[] = 'El nombre debe tener al menos 2 letras.';
    }
    if (!filter_var($datos['correo'], FILTER_VALIDATE_EMAIL)) {
        $errores[] = 'El correo no parece válido.';
    }
    if (mb_strlen($datos['mensaje']) < 10) {
        $errores[] = 'El mensaje es muy corto (mínimo 10 caracteres).';
    }

    // 3) Si todo está bien, guardar con una consulta PREPARADA y redirigir
    if (count($errores) === 0) {
        $guardar = $pdo->prepare('INSERT INTO contactos (nombre, correo, mensaje) VALUES (?, ?, ?)');
        $guardar->execute([$datos['nombre'], $datos['correo'], $datos['mensaje']]);
        header('Location: contacto.php?enviado=1');
        exit;
    }
}

$enviado = isset($_GET['enviado']);
$ultimos = $pdo->query('SELECT nombre, mensaje, creado FROM contactos ORDER BY id DESC LIMIT 5')->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Contacto · Jefe final</title>
  <link rel="stylesheet" href="/assets/fogon.css">
</head>
<body>
<main>
  <div class="migas"><a href="/">🔥 inicio</a> / jefe final / contacto.php</div>
  <div class="kicker">JEFE FINAL · PHP EN TU PROYECTO</div>
  <h1>✉️ Contactanos</h1>

  <?php if ($enviado): ?>
    <div class="tarjeta fuego"><p class="grande" style="font-size:1.5rem">✅ ¡Gracias! Tu mensaje quedó guardado.</p></div>
  <?php endif; ?>

  <?php if (count($errores) > 0): ?>
    <div class="alerta">
      <b>Revisá esto:</b>
      <ul><?php foreach ($errores as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul>
    </div>
  <?php endif; ?>

  <div class="tarjeta">
    <form method="post" novalidate>
      <label>Nombre <input name="nombre" value="<?= e($datos['nombre']) ?>"></label>
      <label>Correo <input name="correo" type="email" value="<?= e($datos['correo']) ?>"></label>
      <label>Mensaje <textarea name="mensaje"><?= e($datos['mensaje']) ?></textarea></label>
      <button>Enviar</button>
    </form>
  </div>

  <h2>Últimos mensajes recibidos</h2>
  <?php foreach ($ultimos as $u): ?>
    <!-- Acá SÍ usamos el escudo e() = htmlspecialchars. Comparalo con el libro de visitas. -->
    <div class="mensaje"><b><?= e($u['nombre']) ?></b> <small><?= e($u['creado']) ?></small><br><?= e($u['mensaje']) ?></div>
  <?php endforeach; ?>
</main>
</body>
</html>
