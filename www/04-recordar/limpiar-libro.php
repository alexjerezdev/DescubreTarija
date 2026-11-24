<?php
// =====================================================================
//  HERRAMIENTA DEL PROFE: vaciar el libro de visitas
//  (por ejemplo, después de la demostración del final de la clase)
// =====================================================================
require 'conexion.php';

$hecho = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['confirmar'] ?? '') === 'LIMPIAR') {
    $pdo->exec('DELETE FROM visitas');
    $pdo->exec("INSERT INTO visitas (nombre, mensaje) VALUES ('Profe', '¡Bienvenidos a la cocina! Dejen su mensaje.')");
    $hecho = true;
}
?>
<!DOCTYPE html>
<html lang="es">
<head><meta charset="utf-8"><title>Limpiar libro</title><link rel="stylesheet" href="/assets/fogon.css"></head>
<body><main>
  <div class="migas"><a href="/">🔥 inicio</a> / <a href="/04-recordar/">nivel 4</a> / limpiar-libro.php</div>
  <h1>🧹 Vaciar el libro de visitas</h1>
  <?php if ($hecho): ?>
    <div class="tarjeta fuego">Listo: el libro quedó limpio. <a href="2-libro.php">Volver al libro</a></div>
  <?php else: ?>
    <div class="tarjeta">
      <form method="post">
        <label>Escribí <code>LIMPIAR</code> para confirmar <input name="confirmar" autocomplete="off"></label>
        <button>Vaciar el libro</button>
      </form>
      <p class="nota">Si el libro quedó tan "roto" que ni carga, esta página igual funciona: no muestra mensajes.</p>
    </div>
  <?php endif; ?>
</main></body></html>
