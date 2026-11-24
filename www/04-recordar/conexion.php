<?php
// =====================================================================
//  NIVEL 4 · LA CONEXIÓN: el cocinero abre la puerta de la despensa
//  Este archivo se INCLUYE en las otras páginas con:
//      require 'conexion.php';
//  Así escribimos la conexión una sola vez.
// =====================================================================

$host    = getenv('DB_HOST') ?: 'db';        // "db" = nombre del contenedor de MySQL en Docker
$base    = getenv('DB_NAME') ?: 'fogon';
$usuario = getenv('DB_USER') ?: 'fogon';
$clave   = getenv('DB_PASS') ?: 'fogon123';

try {
    // PDO = la llave de la despensa. Funciona con MySQL, PostgreSQL, SQLite...
    $pdo = new PDO(
        "mysql:host=$host;dbname=$base;charset=utf8mb4",
        $usuario,
        $clave,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,  // si algo falla, que avise
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,        // filas como arrays con nombre
        ]
    );
} catch (PDOException $e) {
    // Si la despensa está cerrada, mostramos un mensaje claro en vez de una pantalla rota
    http_response_code(500);
    echo '<link rel="stylesheet" href="/assets/fogon.css"><main><h1>🥫 La despensa está cerrada</h1>';
    echo '<div class="alerta">No pude conectarme a MySQL. ¿Está encendido el contenedor <code>fogon-db</code>?<br>';
    echo 'Probá: <code>docker compose ps</code> y <code>docker compose up -d</code></div>';
    echo '<p class="nota">Detalle técnico: ' . htmlspecialchars($e->getMessage()) . '</p></main>';
    exit;
}
