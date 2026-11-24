<?php
require __DIR__ . '/includes/conexion.php';
require_once __DIR__ . '/includes/funciones.php';

requerir_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /recorridos.php');
    exit;
}
if (!csrf_valido()) {
    http_response_code(400);
    poner_flash('danger', 'Solicitud no válida. Intenta de nuevo.');
    header('Location: /recorridos.php');
    exit;
}

$accion = $_POST['accion'] ?? '';
$uid    = usuario_actual()['id'];            // de la sesión, nunca del formulario

// ---------- CREAR ----------
if ($accion === 'crear') {
    $nombre = trim($_POST['nombre'] ?? '');
    $fecha  = trim($_POST['fecha_visita'] ?? '');
    $fecha_ok = null;
    $error = '';

    if (mb_strlen($nombre) < 2 || mb_strlen($nombre) > 80) {
        $error = 'El nombre debe tener entre 2 y 80 caracteres.';
    }
    if ($fecha !== '') {
        $f = DateTime::createFromFormat('!Y-m-d', $fecha);
        if ($f && $f->format('Y-m-d') === $fecha) {
            $fecha_ok = $fecha;
        } else {
            $error = 'La fecha no es válida.';
        }
    }

    if ($error !== '') {
        poner_flash('danger', $error);
        header('Location: /recorridos.php');
        exit;
    }

    $pdo->prepare('INSERT INTO recorridos (usuario_id, nombre, fecha_visita) VALUES (?, ?, ?)')
        ->execute([$uid, $nombre, $fecha_ok]);
    poner_flash('success', 'Recorrido creado. Ahora agrégale destinos.');
    header('Location: /recorrido.php?id=' . (int) $pdo->lastInsertId());
    exit;
}

// ---------- LAS DEMÁS ACCIONES: el recorrido debe ser tuyo ----------
$recorrido_id = (int) ($_POST['recorrido_id'] ?? 0);
$r = recorrido_propio($pdo, $recorrido_id);
if (!$r) {
    poner_flash('danger', 'Ese recorrido no existe.');
    header('Location: /recorridos.php');
    exit;
}
$ir_recorrido = '/recorrido.php?id=' . (int) $r['id'];

// ---------- ELIMINAR RECORRIDO ----------
if ($accion === 'eliminar') {
    $pdo->beginTransaction();
    try {
        $pdo->prepare('DELETE FROM recorrido_destinos WHERE recorrido_id = ?')->execute([$r['id']]);
        $pdo->prepare('DELETE FROM recorridos WHERE id = ?')->execute([$r['id']]);
        $pdo->commit();
    } catch (Throwable $ex) {
        $pdo->rollBack();
        throw $ex;
    }
    poner_flash('info', 'Eliminaste el recorrido «' . $r['nombre'] . '».');
    header('Location: /recorridos.php');
    exit;
}

// ---------- ACCIONES SOBRE UN DESTINO DEL RECORRIDO ----------
$destino_id = (int) ($_POST['destino_id'] ?? 0);

if ($accion === 'agregar') {
    $volver = volver_seguro($_POST['volver'] ?? '', $ir_recorrido);

    $buscar = $pdo->prepare('SELECT nombre FROM destinos WHERE id = ?');
    $buscar->execute([$destino_id]);
    $nombre_destino = $buscar->fetchColumn();

    if ($nombre_destino === false) {
        poner_flash('danger', 'Ese destino no existe.');
    } else {
        $ya = $pdo->prepare('SELECT 1 FROM recorrido_destinos WHERE recorrido_id = ? AND destino_id = ?');
        $ya->execute([$r['id'], $destino_id]);
        if ($ya->fetchColumn()) {
            poner_flash('info', '«' . $nombre_destino . '» ya estaba en ese recorrido.');
        } else {
            $sig = $pdo->prepare('SELECT COALESCE(MAX(orden), 0) + 1 FROM recorrido_destinos WHERE recorrido_id = ?');
            $sig->execute([$r['id']]);
            $pdo->prepare('INSERT INTO recorrido_destinos (recorrido_id, destino_id, orden) VALUES (?, ?, ?)')
                ->execute([$r['id'], $destino_id, (int) $sig->fetchColumn()]);
            poner_flash('success', '«' . $nombre_destino . '» se agregó a «' . $r['nombre'] . '».');
        }
    }
    header('Location: ' . $volver);
    exit;
}

if ($accion === 'quitar') {
    $pdo->prepare('DELETE FROM recorrido_destinos WHERE recorrido_id = ? AND destino_id = ?')
        ->execute([$r['id'], $destino_id]);
    guardar_orden($pdo, (int) $r['id'], ids_en_orden($pdo, (int) $r['id']));   // renumera sin huecos
    poner_flash('info', 'Quitaste un destino del recorrido.');
    header('Location: ' . $ir_recorrido);
    exit;
}

if ($accion === 'subir' || $accion === 'bajar') {
    $ids = ids_en_orden($pdo, (int) $r['id']);
    $pos = array_search($destino_id, $ids, true);
    if ($pos !== false) {
        $nuevo = $accion === 'subir' ? $pos - 1 : $pos + 1;
        if (isset($ids[$nuevo])) {
            [$ids[$pos], $ids[$nuevo]] = [$ids[$nuevo], $ids[$pos]];   // intercambio
            guardar_orden($pdo, (int) $r['id'], $ids);
        }
    }
    header('Location: ' . $ir_recorrido);
    exit;
}

// Acción desconocida
header('Location: /recorridos.php');
exit;