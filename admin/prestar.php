<?php
require_once __DIR__ . '/../functions.php';
requerir_admin();

// Si viene reserva_id, es prestar una reserva existente
$reserva_id = (int)($_GET['reserva_id'] ?? 0);
$libro_id_get = (int)($_GET['libro_id'] ?? 0);

$reserva = null;
$libro_preseleccionado = null;

if ($reserva_id) {
    $stmt = db()->prepare('SELECT r.*, l.titulo, l.autor FROM reservas r JOIN libros l ON r.libro_id = l.id WHERE r.id = ?');
    $stmt->execute([$reserva_id]);
    $reserva = $stmt->fetch();
    if (!$reserva || $reserva['estado'] !== 'reservado') {
        redirect('reservas.php');
    }
} elseif ($libro_id_get) {
    $libro_preseleccionado = obtener_libro($libro_id_get);
    if (!$libro_preseleccionado) {
        redirect('libros.php');
    }
}

// Listar libros disponibles para el select
$libros = db()->query('SELECT * FROM libros ORDER BY titulo ASC')->fetchAll();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $libro_id = (int)($_POST['libro_id'] ?? 0);
    $nombre = trim($_POST['nombre_completo'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $carrera_curso = trim($_POST['carrera_curso'] ?? '');
    $fecha_devolucion = trim($_POST['fecha_devolucion'] ?? '');

    if ($reserva_id) {
        // Prestar una reserva existente
        if ($fecha_devolucion === '') {
            $error = 'Indicá la fecha de devolución.';
        } else {
            if (prestar_reserva($reserva_id, $fecha_devolucion)) {
                redirect('reservas.php?msg=prestado');
            } else {
                $error = 'No se pudo registrar el préstamo.';
            }
        }
    } else {
        // Préstamo directo
        if ($libro_id === 0 || $nombre === '' || $telefono === '' || $carrera_curso === '' || $fecha_devolucion === '') {
            $error = 'Completá todos los campos.';
        } else {
            if (prestar_directo($libro_id, $nombre, $telefono, $carrera_curso, $fecha_devolucion)) {
                redirect('reservas.php?msg=prestado');
            } else {
                $error = 'No hay ejemplares disponibles de ese libro.';
            }
        }
    }
}

$fecha_default = date('Y-m-d', strtotime('+15 days'));

include 'header_admin.php';
?>

<div class="contenedor-estrecho">
  <div class="seccion-titulo">
    <h2><?= $reserva_id ? 'Registrar préstamo de reserva' : 'Nuevo préstamo directo' ?></h2>
  </div>

  <?php if ($error): ?>
    <div class="mensaje mensaje-error"><?= e($error) ?></div>
  <?php endif; ?>

  <div class="form-card">
    <form method="POST" action="prestar.php<?= $reserva_id ? '?reserva_id=' . $reserva_id : '' ?>">
      <?php if ($reserva): ?>
        <div class="mensaje mensaje-info">
          Reserva de <strong><?= e($reserva['nombre_completo']) ?></strong> &middot;
          Libro: <strong><?= e($reserva['titulo']) ?></strong> de <?= e($reserva['autor']) ?>
        </div>
        <div class="form-grupo">
          <label>Nombre</label>
          <input type="text" value="<?= e($reserva['nombre_completo']) ?>" disabled>
        </div>
        <div class="form-grupo">
          <label>Teléfono</label>
          <input type="text" value="<?= e($reserva['telefono']) ?>" disabled>
        </div>
        <div class="form-grupo">
          <label>Carrera/Curso</label>
          <input type="text" value="<?= e($reserva['carrera_curso']) ?>" disabled>
        </div>
      <?php else: ?>
        <div class="form-grupo">
          <label for="libro_id">Libro *</label>
          <select id="libro_id" name="libro_id" required>
            <option value="">Seleccioná un libro...</option>
            <?php foreach ($libros as $l): ?>
              <option value="<?= $l['id'] ?>" <?= ($libro_preseleccionado && $libro_preseleccionado['id'] == $l['id']) ? 'selected' : '' ?>
                <?= $l['cantidad_disponible'] <= 0 ? 'disabled' : '' ?>>
                <?= e($l['titulo']) ?> - <?= e($l['autor']) ?>
                <?= $l['cantidad_disponible'] <= 0 ? '(sin disponibles)' : ' (' . $l['cantidad_disponible'] . ' disp.)' ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-grupo">
          <label for="nombre_completo">Nombre completo *</label>
          <input type="text" id="nombre_completo" name="nombre_completo" required value="<?= old('nombre_completo') ?>">
        </div>
        <div class="form-grupo">
          <label for="telefono">Teléfono *</label>
          <input type="text" id="telefono" name="telefono" required value="<?= old('telefono') ?>">
        </div>
        <div class="form-grupo">
          <label for="carrera_curso">Carrera o curso *</label>
          <input type="text" id="carrera_curso" name="carrera_curso" required value="<?= old('carrera_curso') ?>">
        </div>
      <?php endif; ?>

      <div class="form-grupo">
        <label for="fecha_devolucion">Fecha de devolución *</label>
        <input type="date" id="fecha_devolucion" name="fecha_devolucion" required value="<?= e($_POST['fecha_devolucion'] ?? $fecha_default) ?>">
      </div>

      <div class="form-acciones">
        <button type="submit" class="btn btn-primario">Registrar préstamo</button>
        <a href="reservas.php" class="btn btn-secundario">Cancelar</a>
      </div>
    </form>
  </div>
</div>

<?php include 'footer_admin.php'; ?>
