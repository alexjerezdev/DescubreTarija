<?php
require __DIR__ . '/04-recordar/conexion.php';
$tablas = ['categorias','destinos','imagenes','usuarios','favoritos','recorridos','recorrido_destinos'];
$tilde  = $pdo->query('SELECT nombre FROM destinos WHERE id = 4')->fetchColumn();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>DescubreTarija · prueba</title>
  <link rel="stylesheet" href="/assets/fogon.css">
</head>
<body>
<main>
  <h1>DescubreTarija · prueba de conexión</h1>
  <div class="tarjeta fuego">
    <p class="ok">PHP <?= PHP_VERSION ?> conectado a MySQL</p>
    <table>
      <tr><th>Tabla</th><th>Filas</th></tr>
      <?php foreach ($tablas as $t): ?>
        <tr><td><?= $t ?></td><td><?= $pdo->query("SELECT COUNT(*) FROM `$t`")->fetchColumn() ?></td></tr>
      <?php endforeach; ?>
    </table>
    <p>Prueba de tildes: <b><?= htmlspecialchars($tilde, ENT_QUOTES, 'UTF-8') ?></b></p>
  </div>
</main>
</body>
</html>