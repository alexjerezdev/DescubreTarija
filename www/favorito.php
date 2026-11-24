<?php
require __DIR__ . '/includes/conexion.php';
require_once __DIR__ . '/includes/funciones.php';

requerir_login();

// Solo acepta POST. Si lo abren por la URL, los mandamos a su lista.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /favoritos.php');
    exit;
}

if (!csrf_valido()) {
    http_response_code(400);
    poner_flash('danger', 'Solicitud no válida. Intenta de nuevo.');
    header('Location: /destinos.php');
    exit;
}

$volver     = volver_seguro($_POST['volver'] ?? '', '/favoritos.php');
$destino_id = (int) ($_POST['destino_id'] ?? 0);
$accion     = $_POST['accion'] ?? '';
$usuario_id = usuario_actual()['id'];          // de la sesión, nunca del formulario

// ¿El destino existe?
$buscar = $pdo->prepare('SELECT nombre FROM destinos WHERE id = ?');
$buscar->execute([$destino_id]);
$nombre = $buscar->fetchColumn();

if ($nombre === false) {
    poner_flash('danger', 'Ese destino no existe.');
    header('Location: ' . $volver);
    exit;
}

if ($accion === 'agregar') {
    // INSERT IGNORE: si ya estaba (UNIQUE usuario_id + destino_id), no duplica ni da error
    $pdo->prepare('INSERT IGNORE INTO favoritos (usuario_id, destino_id) VALUES (?, ?)')
        ->execute([$usuario_id, $destino_id]);
    poner_flash('success', '«' . $nombre . '» se guardó en tus favoritos.');
} elseif ($accion === 'quitar') {
    $pdo->prepare('DELETE FROM favoritos WHERE usuario_id = ? AND destino_id = ?')
        ->execute([$usuario_id, $destino_id]);
    poner_flash('info', '«' . $nombre . '» se quitó de tus favoritos.');
}

header('Location: ' . $volver);
exit;