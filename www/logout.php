<?php
require_once __DIR__ . '/includes/funciones.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_valido()) {
    unset($_SESSION['usuario'], $_SESSION['csrf']);
    session_regenerate_id(true);
    poner_flash('info', 'Cerraste sesión.');
}
header('Location: /index.php');
exit;