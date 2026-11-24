<?php
// =====================================================================
//  OPERACIÓN FOGÓN · el mapa de la cocina (página de inicio)
//  Esta página ya es PHP: fijate que muestra la hora del SERVIDOR
//  y revisa si la despensa (MySQL) está encendida.
// =====================================================================

// ¿La despensa responde? Intentamos conectarnos sin romper la página.
$despensa = false;
try {
    $host = getenv('DB_HOST') ?: 'db';
    $pdo  = new PDO("mysql:host=$host;dbname=fogon;charset=utf8mb4", 'fogon', 'fogon123',
                    [PDO::ATTR_TIMEOUT => 2]);
    $despensa = true;
} catch (Throwable $e) {
    $despensa = false;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>OPERACIÓN FOGÓN · PHP desde cero</title>
  <link rel="stylesheet" href="/assets/fogon.css">
</head>
<body>
<main>
  <div class="kicker">TECNOLOGÍA WEB I · FORJA WEB · UPDS TARIJA</div>
  <h1>🔥 OPERACIÓN FOGÓN</h1>
  <p class="grande" style="font-size:1.4rem">PHP desde cero: hoy el cocinero entra a la cocina.</p>

  <div class="tarjeta fuego">
    🧑‍🍳 Cocinero (PHP <?php echo PHP_VERSION; ?>): <span class="ok">encendido</span> ·
    🕒 Hora del servidor: <b><?php echo date('H:i:s'); ?></b> ·
    🥫 Despensa (MySQL):
    <?php if ($despensa): ?>
      <span class="ok">encendida</span>
    <?php else: ?>
      <span class="mal">apagada</span> <span class="nota">(los niveles 0 a 3 funcionan igual)</span>
    <?php endif; ?>
  </div>

  <h2>El mapa de la cocina</h2>
  <div class="mapa">
    <a class="nivel" href="/00-gancho/">
      <div class="num">NIVEL 0 · EL GANCHO</div><b>HTML no sabe qué hora es</b>
      <span>La misma página en HTML y en PHP. ¿Cuál está viva?</span>
    </a>
    <a class="nivel" href="/01-hablar/">
      <div class="num">NIVEL 1 · HABLAR</div><b>echo y variables</b>
      <span>PHP escribe HTML por vos. Tu tarjeta de presentación dinámica.</span>
    </a>
    <a class="nivel" href="/02-decidir/">
      <div class="num">NIVEL 2 · DECIDIR Y REPETIR</div><b>if, arrays y foreach</b>
      <span>3 líneas de PHP que generan todo un menú.</span>
    </a>
    <a class="nivel" href="/03-escuchar/">
      <div class="num">NIVEL 3 · ESCUCHAR</div><b>Formularios: GET y POST</b>
      <span>El Oráculo responde. Y un login que muestra tu contraseña en la URL.</span>
    </a>
    <a class="nivel" href="/04-recordar/">
      <div class="num">NIVEL 4 · RECORDAR</div><b>PHP + MySQL con PDO</b>
      <span>El menú desde la base de datos y el libro de visitas.</span>
    </a>
    <a class="nivel" href="/05-errores/">
      <div class="num">MUSEO · LOS 5 ERRORES</div><b>Los errores que todos cometen</b>
      <span>Rompelos a propósito y aprendé a leer lo que dice PHP.</span>
    </a>
    <a class="nivel" href="/06-reto-final/">
      <div class="num">JEFE FINAL</div><b>PHP en tu proyecto</b>
      <span>Un formulario de contacto listo para llevar a tu FORJA WEB.</span>
    </a>
  </div>

  <p class="pie">Los archivos están en la carpeta <code>www</code>. Abrí cualquiera con VS Code, cambiá algo, guardá y recargá el navegador.<br>
  phpMyAdmin (la ventana a la despensa): <a href="http://localhost:8091" target="_blank">http://localhost:8091</a><br>
  M.Sc. Ing. Juan Pablo Cruz Ovando · jpcruzovando.com</p>
</main>
</body>
</html>
