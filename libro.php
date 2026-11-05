<?php
require_once __DIR__ . '/functions.php';

$libro_id = (int)($_GET['id'] ?? 0);
$libro = obtener_libro($libro_id);

if (!$libro) {
    redirect('index.php');
}

$error = '';
$exito = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre_completo'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $carrera_curso = trim($_POST['carrera_curso'] ?? '');

    if ($nombre === '' || $telefono === '' || $carrera_curso === '') {
        $error = 'Completá todos los campos.';
    } elseif ($libro['cantidad_disponible'] <= 0) {
        $error = 'No hay ejemplares disponibles para reservar.';
    } else {
        if (crear_reserva($libro_id, $nombre, $telefono, $carrera_curso)) {
            redirect('index.php?msg=reservado');
        } else {
            $error = 'No se pudo realizar la reserva. Intentá de nuevo.';
        }
    }
}

$reservas_activas = obtener_reservas_libro($libro_id);

include __DIR__ . '/header.php';
?>

<div class="contenedor">
  <div style="margin-bottom: 1rem;">
    <a href="index.php" style="color: var(--azul-700); font-weight: 600;">&larr; Volver al catálogo</a>
  </div>

  <?php if ($error): ?>
    <div class="mensaje mensaje-error"><?= e($error) ?></div>
  <?php endif; ?>

  <div class="detalle-libro">
    <h2><?= e($libro['titulo']) ?></h2>
    <p class="detalle-autor"><?= e($libro['autor']) ?></p>

    <div class="detalle-datos">
      <?php if ($libro['carrera']): ?>
        <div class="detalle-item"><label>Carrera</label><span><?= e($libro['carrera']) ?></span></div>
      <?php endif; ?>
      <?php if ($libro['curso']): ?>
        <div class="detalle-item"><label>Curso</label><span><?= e($libro['curso']) ?></span></div>
      <?php endif; ?>
      <?php if ($libro['materia']): ?>
        <div class="detalle-item"><label>Materia</label><span><?= e($libro['materia']) ?></span></div>
      <?php endif; ?>
      <div class="detalle-item"><label>Ejemplares totales</label><span><?= (int)$libro['cantidad_total'] ?></span></div>
      <div class="detalle-item"><label>Disponibles</label><span><?= (int)$libro['cantidad_disponible'] ?></span></div>
    </div>

    <div class="detalle-disponibilidad <?= $libro['cantidad_disponible'] > 0 ? 'detalle-disp-si' : 'detalle-disp-no' ?>">
      <?= $libro['cantidad_disponible'] > 0
        ? 'Ejemplares disponibles para reservar'
        : 'No hay ejemplares disponibles en este momento' ?>
    </div>

    <?php if ($libro['cantidad_disponible'] > 0): ?>
      <div class="seccion-titulo"><h2>Reservar este libro</h2></div>
      <form class="form-card" method="POST" action="libro.php?id=<?= $libro_id ?>">
        <div class="form-grupo">
          <label for="nombre_completo">Nombre completo</label>
          <input type="text" id="nombre_completo" name="nombre_completo" required value="<?= old('nombre_completo') ?>">
        </div>
        <div class="form-grupo">
          <label for="telefono">Teléfono</label>
          <input type="text" id="telefono" name="telefono" required value="<?= old('telefono') ?>">
        </div>
        <div class="form-grupo">
          <label for="carrera_curso">Carrera o curso</label>
          <input type="text" id="carrera_curso" name="carrera_curso" required placeholder="Ej: Tecnicatura en Sistemas, 1er año" value="<?= old('carrera_curso') ?>">
        </div>
        <div class="form-acciones">
          <button type="submit" class="btn btn-primario">Reservar libro</button>
        </div>
      </form>
    <?php endif; ?>

    <?php if (!empty($reservas_activas)): ?>
      <div class="seccion-titulo" style="margin-top: 2rem;"><h2>Reservas y préstamos activos</h2></div>
      <div class="tabla-contenedor">
        <table class="tabla">
          <thead>
            <tr><th>Persona</th><th>Teléfono</th><th>Carrera/Curso</th><th>Estado</th><th>Fecha</th></tr>
          </thead>
          <tbody>
            <?php foreach ($reservas_activas as $r): ?>
              <tr>
                <td><?= e($r['nombre_completo']) ?></td>
                <td><?= e($r['telefono']) ?></td>
                <td><?= e($r['carrera_curso']) ?></td>
                <td><span class="badge badge-<?= $r['estado'] ?>"><?= e($r['estado']) ?></span></td>
                <td><?= e(date('d/m/Y H:i', strtotime($r['fecha_prestamo'] ?? $r['fecha_reserva']))) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>
