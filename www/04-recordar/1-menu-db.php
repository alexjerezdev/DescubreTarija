<?php
// =====================================================================
//  NIVEL 4 · EL MENÚ DESDE LA BASE DE DATOS
//  Ahora los platos NO están en el código: están en MySQL (tabla "menu").
//  Cambiá un precio en phpMyAdmin (http://localhost:8091), recargá… y listo.
// =====================================================================

require 'conexion.php';      // trae la variable $pdo

$categoria = $_GET['cat'] ?? '';     // filtro opcional: ?cat=Postre

if ($categoria === '') {
    // 1) Consulta simple: todos los platos
    $platos = $pdo->query('SELECT plato, categoria, precio, picante FROM menu ORDER BY categoria, precio')
                  ->fetchAll();
} else {
    // 2) Consulta PREPARADA: el dato del usuario va aparte, en el lugar del ?
    //    (así nadie puede "colarse" dentro del SQL; mañana lo vemos en acción)
    $consulta = $pdo->prepare('SELECT plato, categoria, precio, picante FROM menu WHERE categoria = ? ORDER BY precio');
    $consulta->execute([$categoria]);
    $platos = $consulta->fetchAll();
}

function e($t) { return htmlspecialchars((string) $t, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Nivel 4 · Menú desde MySQL</title>
  <link rel="stylesheet" href="/assets/fogon.css">
</head>
<body>
<main>
  <div class="migas"><a href="/">🔥 inicio</a> / <a href="/04-recordar/">nivel 4</a> / 1-menu-db.php</div>
  <div class="kicker">NIVEL 4 · RECORDAR</div>
  <h1>🥫 El menú sale de la despensa</h1>

  <p>
    <a class="chip fuego" href="?">Todos</a>
    <?php foreach (['Entrada', 'Fondo', 'Postre', 'Bebida'] as $c): ?>
      <a class="chip cian" href="?cat=<?= $c ?>"><?= $c ?></a>
    <?php endforeach; ?>
  </p>

  <div class="tarjeta fuego">
    <table>
      <tr><th>Plato</th><th>Categoría</th><th>Precio</th><th></th></tr>
      <?php foreach ($platos as $p): ?>
        <tr>
          <td><?= e($p['plato']) ?></td>
          <td><?= e($p['categoria']) ?></td>
          <td><?= e($p['precio']) ?> Bs</td>
          <td><?= $p['picante'] ? '🌶️' : '' ?></td>
        </tr>
      <?php endforeach; ?>
    </table>
    <p><b><?= count($platos) ?></b> platos encontrados<?= $categoria ? ' en «' . e($categoria) . '»' : '' ?>.</p>
  </div>

  <div class="tarjeta">
    <b>🎯 Misión:</b> entrá a <a href="http://localhost:8091" target="_blank">phpMyAdmin</a> → base <code>fogon</code> →
    tabla <code>menu</code> → agregá un plato nuevo. Volvé y recargá. <b>No tocaste el código y la página cambió.</b>
  </div>
</main>
</body>
</html>
