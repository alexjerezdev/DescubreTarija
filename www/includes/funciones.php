<?php
// Funciones compartidas. Inicia la sesión una sola vez.

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
    session_start();
}

// Escudo anti-XSS: usar en TODO dato que se muestre en pantalla
function e($texto): string
{
    return htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8');
}

// ---- Sesión ----
function esta_logueado(): bool
{
    return isset($_SESSION['usuario']);
}

function usuario_actual(): ?array
{
    return $_SESSION['usuario'] ?? null;
}

function es_admin(): bool
{
    return esta_logueado() && ($_SESSION['usuario']['rol'] ?? '') === 'admin';
}

function requerir_login(): void
{
    if (!esta_logueado()) {
        poner_flash('warning', 'Inicia sesión para continuar.');
        header('Location: /login.php');
        exit;
    }
}

function requerir_admin(): void
{
    if (!es_admin()) {
        poner_flash('danger', 'Solo el administrador puede entrar aquí.');
        header('Location: /login.php');
        exit;
    }
}

// ---- Mensajes flash (se muestran una vez y desaparecen) ----
// tipo: success | danger | warning | info
function poner_flash(string $tipo, string $mensaje): void
{
    $_SESSION['flash'] = ['tipo' => $tipo, 'mensaje' => $mensaje];
}

function mostrar_flash(): void
{
    if (!isset($_SESSION['flash'])) {
        return;
    }
    $f = $_SESSION['flash'];
    unset($_SESSION['flash']);
    echo '<div class="container mt-3"><div class="alert alert-' . e($f['tipo']) . ' alert-dismissible fade show" role="alert">';
    echo e($f['mensaje']);
    echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button></div></div>';
}

// ---- Navbar: marca el enlace de la página actual ----
function nav_activo(string $pagina, string $activo): string
{
    return $pagina === $activo ? ' active' : '';
}



// ---- Consulta base de destinos (con categoría y primera foto) ----
// Uso: $pdo->query(SQL_DESTINOS . ' WHERE d.destacado = 1 ORDER BY d.nombre')
const SQL_DESTINOS = 'SELECT d.id, d.nombre, d.descripcion, d.ubicacion, d.categoria_id,
        c.nombre AS categoria,
        (SELECT i.url FROM imagenes i WHERE i.destino_id = d.id ORDER BY i.id LIMIT 1) AS foto
    FROM destinos d
    LEFT JOIN categorias c ON c.id = d.categoria_id';

// Si el dato es NULL, vacío o "POR VERIFICAR", devuelve un texto alternativo
function dato($texto, string $vacio = 'Por verificar'): string
{
    $texto = trim((string) $texto);
    return ($texto === '' || strcasecmp($texto, 'POR VERIFICAR') === 0) ? $vacio : $texto;
}

// Devuelve la URL de la foto SOLO si el archivo existe en www/ (si no, null)
function foto_url(?string $ruta): ?string
{
    if (!$ruta || str_contains($ruta, '..')) {
        return null;
    }
    $ruta = ltrim($ruta, '/');
    return is_file(__DIR__ . '/../' . $ruta) ? '/' . $ruta : null;
}


// ---- Fase 5: favoritos y formularios seguros ----

// Token CSRF: se genera una vez por sesión y se envía oculto en cada formulario que modifica datos
function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_campo(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

function csrf_valido(): bool
{
    return isset($_POST['csrf'], $_SESSION['csrf'])
        && hash_equals($_SESSION['csrf'], (string) $_POST['csrf']);
}

// Solo permite volver a rutas internas del sitio (evita "open redirect")
function volver_seguro($ruta, string $por_defecto): string
{
    $ruta = is_string($ruta) ? $ruta : '';
    if ($ruta === '' || $ruta[0] !== '/' || str_starts_with($ruta, '//')
        || str_contains($ruta, '\\') || preg_match('/[\r\n]/', $ruta)) {
        return $por_defecto;
    }
    return $ruta;
}

// IDs de los destinos favoritos del usuario actual (una sola consulta por petición)
function favoritos_ids(PDO $pdo): array
{
    static $ids = null;
    if (!esta_logueado()) {
        return [];
    }
    if ($ids === null) {
        $c = $pdo->prepare('SELECT destino_id FROM favoritos WHERE usuario_id = ?');
        $c->execute([usuario_actual()['id']]);
        $ids = array_map('intval', $c->fetchAll(PDO::FETCH_COLUMN));
    }
    return $ids;
}


// ---- Fase 6: recorridos ----

// Devuelve el recorrido SOLO si pertenece al usuario de la sesión (si no, null)
function recorrido_propio(PDO $pdo, int $id): ?array
{
    $c = $pdo->prepare('SELECT * FROM recorridos WHERE id = ? AND usuario_id = ?');
    $c->execute([$id, usuario_actual()['id'] ?? 0]);
    return $c->fetch() ?: null;
}

// 2026-12-05 -> 05/12/2026
function fecha_es(?string $fecha, string $vacio = 'Sin fecha'): string
{
    if (!$fecha) {
        return $vacio;
    }
    $f = DateTime::createFromFormat('Y-m-d', $fecha);
    return $f ? $f->format('d/m/Y') : $vacio;
}

// IDs de destino de un recorrido, en su orden actual
function ids_en_orden(PDO $pdo, int $recorrido_id): array
{
    $c = $pdo->prepare('SELECT destino_id FROM recorrido_destinos WHERE recorrido_id = ? ORDER BY orden, id');
    $c->execute([$recorrido_id]);
    return array_map('intval', $c->fetchAll(PDO::FETCH_COLUMN));
}

// Reescribe orden = 1, 2, 3... según la lista de IDs recibida
function guardar_orden(PDO $pdo, int $recorrido_id, array $ids): void
{
    $u = $pdo->prepare('UPDATE recorrido_destinos SET orden = ? WHERE recorrido_id = ? AND destino_id = ?');
    $pdo->beginTransaction();
    try {
        foreach (array_values($ids) as $i => $destino_id) {
            $u->execute([$i + 1, $recorrido_id, $destino_id]);
        }
        $pdo->commit();
    } catch (Throwable $ex) {
        $pdo->rollBack();
        throw $ex;
    }
}