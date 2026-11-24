<?php
// Conexión a MySQL con PDO. Se incluye con: require __DIR__ . '/includes/conexion.php';
// Deja disponible la variable $pdo.

$host    = getenv('DB_HOST') ?: 'db';
$base    = getenv('DB_NAME') ?: 'descubretarija';
$usuario = getenv('DB_USER') ?: 'tarija';
$clave   = getenv('DB_PASS') ?: 'tarija123';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$base;charset=utf8mb4",
        $usuario,
        $clave,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $ex) {
    http_response_code(500);
    echo '<!DOCTYPE html><html lang="es"><head><meta charset="utf-8">';
    echo '<link rel="stylesheet" href="/assets/css/tarija.css"></head><body>';
    echo '<main class="container py-5"><h1>No hay conexión con la base de datos</h1>';
    echo '<p>Revisa que el contenedor <code>tarija-db</code> esté encendido: <code>docker compose ps</code></p>';
    echo '<p class="text-muted small">Detalle técnico: ' . htmlspecialchars($ex->getMessage()) . '</p></main></body></html>';
    exit;
}