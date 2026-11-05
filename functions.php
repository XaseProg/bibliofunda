<?php
// ============================================================
// Funciones auxiliares
// ============================================================

require_once __DIR__ . '/config.php';

/** Escapa texto para salida HTML */
function e($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

/** Redirige a una URL */
function redirect($url) {
    header("Location: $url");
    exit;
}

/** Devuelve el valor de un campo POST o vacío */
function old($field) {
    return e($_POST[$field] ?? '');
}

// ============================================================
// Autenticación del administrador
// ============================================================

function iniciar_sesion() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function login_admin($usuario, $password) {
    iniciar_sesion();
    $stmt = db()->prepare('SELECT * FROM administradores WHERE usuario = ? AND password = ?');
    $stmt->execute([$usuario, $password]);
    $admin = $stmt->fetch();
    if ($admin) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_usuario'] = $admin['usuario'];
        return true;
    }
    return false;
}

function logout_admin() {
    iniciar_sesion();
    session_destroy();
}

function es_admin() {
    iniciar_sesion();
    return isset($_SESSION['admin_id']);
}

function requerir_admin() {
    if (!es_admin()) {
        redirect('login.php');
    }
}

// ============================================================
// Lógica de libros
// ============================================================

function obtener_libros($busqueda = '', $campo = '') {
    $sql = 'SELECT * FROM libros WHERE 1=1';
    $params = [];

    $busqueda = trim($busqueda);
    if ($busqueda !== '') {
        if ($campo && in_array($campo, ['titulo','autor','carrera','curso','materia'])) {
            $sql .= " AND $campo LIKE ?";
            $params[] = "%$busqueda%";
        } else {
            $sql .= " AND (titulo LIKE ? OR autor LIKE ? OR carrera LIKE ? OR curso LIKE ? OR materia LIKE ?)";
            $like = "%$busqueda%";
            $params = array_fill(0, 5, $like);
        }
    }
    $sql .= ' ORDER BY titulo ASC';
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function obtener_libro($id) {
    $stmt = db()->prepare('SELECT * FROM libros WHERE id = ?');
    $stmt->execute([$id]);
    return $stmt->fetch() ?: null;
}

function crear_libro($datos) {
    $stmt = db()->prepare('
        INSERT INTO libros (titulo, autor, carrera, curso, materia, cantidad_total, cantidad_disponible)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ');
    $stmt->execute([
        $datos['titulo'],
        $datos['autor'],
        $datos['carrera'] ?: null,
        $datos['curso'] ?: null,
        $datos['materia'] ?: null,
        (int)$datos['cantidad_total'],
        (int)$datos['cantidad_total'],
    ]);
}

function actualizar_libro($id, $datos) {
    $libro = obtener_libro($id);
    if (!$libro) return;

    $diff = (int)$datos['cantidad_total'] - (int)$libro['cantidad_total'];
    $nueva_disp = (int)$libro['cantidad_disponible'] + $diff;
    if ($nueva_disp < 0) $nueva_disp = 0;

    $stmt = db()->prepare('
        UPDATE libros SET titulo=?, autor=?, carrera=?, curso=?, materia=?, cantidad_total=?, cantidad_disponible=?
        WHERE id=?
    ');
    $stmt->execute([
        $datos['titulo'],
        $datos['autor'],
        $datos['carrera'] ?: null,
        $datos['curso'] ?: null,
        $datos['materia'] ?: null,
        (int)$datos['cantidad_total'],
        $nueva_disp,
        $id,
    ]);
}

function eliminar_libro($id) {
    $stmt = db()->prepare('DELETE FROM libros WHERE id = ?');
    $stmt->execute([$id]);
}

// ============================================================
// Lógica de reservas y préstamos
// ============================================================

function obtener_reservas($filtro_estado = '') {
    $sql = 'SELECT r.*, l.titulo, l.autor FROM reservas r
            JOIN libros l ON r.libro_id = l.id';
    $params = [];
    if ($filtro_estado && in_array($filtro_estado, ['reservado','prestado','devuelto','cancelado'])) {
        $sql .= ' WHERE r.estado = ?';
        $params[] = $filtro_estado;
    }
    $sql .= ' ORDER BY r.fecha_reserva DESC';
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function obtener_reservas_libro($libro_id) {
    $stmt = db()->prepare('
        SELECT * FROM reservas WHERE libro_id = ? AND estado IN ("reservado","prestado")
        ORDER BY fecha_reserva ASC
    ');
    $stmt->execute([$libro_id]);
    return $stmt->fetchAll();
}

function crear_reserva($libro_id, $nombre, $telefono, $carrera_curso) {
    $libro = obtener_libro($libro_id);
    if (!$libro || $libro['cantidad_disponible'] <= 0) {
        return false;
    }
    $stmt = db()->prepare('
        INSERT INTO reservas (libro_id, nombre_completo, telefono, carrera_curso, estado)
        VALUES (?, ?, ?, ?, "reservado")
    ');
    $stmt->execute([$libro_id, $nombre, $telefono, $carrera_curso]);
    // Descontar 1 disponible
    db()->prepare('UPDATE libros SET cantidad_disponible = cantidad_disponible - 1 WHERE id = ?')
        ->execute([$libro_id]);
    return true;
}

function prestar_reserva($reserva_id, $fecha_devolucion) {
    $stmt = db()->prepare('SELECT * FROM reservas WHERE id = ?');
    $stmt->execute([$reserva_id]);
    $reserva = $stmt->fetch();
    if (!$reserva || $reserva['estado'] !== 'reservado') return false;

    db()->prepare('
        UPDATE reservas SET estado = "prestado", fecha_prestamo = NOW(), fecha_devolucion = ?
        WHERE id = ?
    ')->execute([$fecha_devolucion, $reserva_id]);
    return true;
}

function devolver_reserva($reserva_id) {
    $stmt = db()->prepare('SELECT * FROM reservas WHERE id = ?');
    $stmt->execute([$reserva_id]);
    $reserva = $stmt->fetch();
    if (!$reserva || $reserva['estado'] !== 'prestado') return false;

    db()->prepare('
        UPDATE reservas SET estado = "devuelto", fecha_real_devolucion = NOW()
        WHERE id = ?
    ')->execute([$reserva_id]);
    // Devolver 1 al disponible
    db()->prepare('UPDATE libros SET cantidad_disponible = cantidad_disponible + 1 WHERE id = ?')
        ->execute([$reserva['libro_id']]);
    return true;
}

function cancelar_reserva($reserva_id) {
    $stmt = db()->prepare('SELECT * FROM reservas WHERE id = ?');
    $stmt->execute([$reserva_id]);
    $reserva = $stmt->fetch();
    if (!$reserva || $reserva['estado'] !== 'reservado') return false;

    db()->prepare('UPDATE reservas SET estado = "cancelado" WHERE id = ?')
        ->execute([$reserva_id]);
    // Devolver 1 al disponible
    db()->prepare('UPDATE libros SET cantidad_disponible = cantidad_disponible + 1 WHERE id = ?')
        ->execute([$reserva['libro_id']]);
    return true;
}

/** Crea un préstamo directo (sin reserva previa) */
function prestar_directo($libro_id, $nombre, $telefono, $carrera_curso, $fecha_devolucion) {
    $libro = obtener_libro($libro_id);
    if (!$libro || $libro['cantidad_disponible'] <= 0) return false;

    db()->prepare('
        INSERT INTO reservas (libro_id, nombre_completo, telefono, carrera_curso, estado, fecha_prestamo, fecha_devolucion)
        VALUES (?, ?, ?, ?, "prestado", NOW(), ?)
    ')->execute([$libro_id, $nombre, $telefono, $carrera_curso, $fecha_devolucion]);
    db()->prepare('UPDATE libros SET cantidad_disponible = cantidad_disponible - 1 WHERE id = ?')
        ->execute([$libro_id]);
    return true;
}
