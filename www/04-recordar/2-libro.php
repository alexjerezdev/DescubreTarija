<?php
// =====================================================================
//  NIVEL 4 · EL LIBRO DE VISITAS
//  1) Si llega el formulario (POST) -> guardamos el mensaje en MySQL (INSERT)
//  2) Después redirigimos a la misma página (así F5 no lo guarda dos veces)
//  3) Mostramos los últimos mensajes (SELECT)
// =====================================================================

require 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre  = trim($_POST['nombre']  ?? '');
    $mensaje = trim($_POST['mensaje'] ?? '');

    if ($nombre !== '' && $mensaje !== '') {
        // Consulta PREPARADA: los datos van aparte, en los lugares de los ?
        $guardar = $pdo->prepare('INSERT INTO visitas (nombre, mensaje) VALUES (?, ?)');
        $guardar->execute([mb_substr($nombre, 0, 60), mb_substr($mensaje, 0, 280)]);
    }
    header('Location: 2-libro.php');     // patrón PRG: Post -> Redirect -> Get
    exit;
}

$mensajes = $pdo->query('SELECT nombre, mensaje, creado FROM visitas ORDER BY id DESC LIMIT 30')->fetchAll();
$total    = $pdo->query('SELECT COUNT(*) FROM visitas')->fetchColumn();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Nivel 4 · Libro de visitas</title>
  <link rel="stylesheet" href="/assets/fogon.css">
</head>
<body>
<main>
  <div class="migas"><a href="/">🔥 inicio</a> / <a href="/04-recordar/">nivel 4</a> / 2-libro.php</div>
  <div class="kicker">NIVEL 4 · RECORDAR</div>
  <h1>📖 Libro de visitas</h1>

  <div class="tarjeta">
    <form method="post">
      <label>Tu nombre <input name="nombre" maxlength="60" required></label>
      <label>Tu mensaje <textarea name="mensaje" maxlength="280" required></textarea></label>
      <button>Firmar el libro</button>
    </form>
  </div>

  <h2><?= $total ?> mensajes guardados en MySQL</h2>
  <?php foreach ($mensajes as $m): ?>
    <div class="mensaje">
      <!-- ⚠️ OJO: a estas dos líneas les FALTA algo que sí tiene el Oráculo.
           No lo arregles todavía: lo descubrimos al final de la clase. -->
      <b><?= $m['nombre'] ?></b> <small><?= $m['creado'] ?></small><br>
      <?= $m['mensaje'] ?>
    </div>
  <?php endforeach; ?>
</main>
</body>
</html>
