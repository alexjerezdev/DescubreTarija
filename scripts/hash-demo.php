<?php
// Pone contrasenas REALES (hash bcrypt) a los usuarios demo del init.sql.
// Ejecutar:  Get-Content .\scripts\hash-demo.php | docker exec -i tarija-web php
// Solo DEMO. Antes de la defensa cambia la clave del admin.
$pdo = new PDO(
    'mysql:host=' . (getenv('DB_HOST') ?: 'db') . ';dbname=' . (getenv('DB_NAME') ?: 'descubretarija') . ';charset=utf8mb4',
    getenv('DB_USER') ?: 'tarija',
    getenv('DB_PASS') ?: 'tarija123',
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

$claves = [
    'admin@descubretarija.bo' => 'Admin2026!',
    'maria@correo.com'        => 'Demo2026!',
    'jose@correo.com'         => 'Demo2026!',
    'lucia@correo.com'        => 'Demo2026!',
    'andres@correo.com'       => 'Demo2026!',
];

$cambiar = $pdo->prepare('UPDATE usuarios SET password_hash = ? WHERE email = ?');
foreach ($claves as $email => $clave) {
    $cambiar->execute([password_hash($clave, PASSWORD_DEFAULT), $email]);
    echo "$email -> OK\n";
}