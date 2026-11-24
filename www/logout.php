<?php
require_once __DIR__ . '/includes/funciones.php';

unset($_SESSION['usuario']);
session_regenerate_id(true);
poner_flash('info', 'Cerraste sesión.');
header('Location: /index.php');
exit;