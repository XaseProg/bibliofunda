<?php
require_once __DIR__ . '/functions.php';

$busqueda = $_GET['q'] ?? '';
$campo = $_GET['campo'] ?? '';
$libros = obtener_libros($busqueda, $campo);
include __DIR__ . '/header.php';
?>

<div class="contenedor">
  <?php if (!empty($_GET['msg'])): ?>
    <div class="mensaje mensaje-exito">
      <?php
        $msgs = [
          'reservado' => 'Reserva realizada con éxito. Acercate al instituto para retirar el libro.',
          'cancelado' => 'La reserva fue cancelada.',
        ];
        echo e($msgs[$_GET['msg']] ?? '');
      ?>
    </div>
  <?php endif; ?>

  <!-- Buscador -->
  <div class="buscador">
    <div class="buscador-titulo">Buscar libros</div>
    <form class="buscador-form" method="GET" action="index.php">
      <input type="text" name="q" class="buscador-input" placeholder="Escribí título, autor, carrera, curso o materia..." value="<?= e($busqueda) ?>">
      <select name="campo" class="buscador-select">
        <option value="">Todos los campos</option>
        <option value="titulo" <?= $campo === 'titulo' ? 'selected' : '' ?>>Título</option>
        <option value="autor" <?= $campo === 'autor' ? 'selected' : '' ?>>Autor</option>
        <option value="carrera" <?= $campo === 'carrera' ? 'selected' : '' ?>>Carrera</option>
        <option value="curso" <?= $campo === 'curso' ? 'selected' : '' ?>>Curso</option>
        <option value="materia" <?= $campo === 'materia' ? 'selected' : '' ?>>Materia</option>
      </select>
      <button type="submit" class="btn-buscar">Buscar</button>
    </form>
  </div>

  <?php if (empty($libros)): ?>
    <div class="vacio">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
      </svg>
      <h3>No se encontraron libros</h3>
      <p>Probá con otra búsqueda o cambiá el campo de búsqueda.</p>
    </div>
  <?php else: ?>
    <div class="libros-grid">
      <?php foreach ($libros as $libro): ?>
        <div class="libro-card">
          <div class="libro-card-titulo"><?= e($libro['titulo']) ?></div>
          <div class="libro-card-autor"><?= e($libro['autor']) ?></div>
          <div class="libro-card-datos">
            <?php if ($libro['carrera']): ?>
              <span class="libro-tag"><?= e($libro['carrera']) ?></span>
            <?php endif; ?>
            <?php if ($libro['curso']): ?>
              <span class="libro-tag"><?= e($libro['curso']) ?></span>
            <?php endif; ?>
            <?php if ($libro['materia']): ?>
              <span class="libro-tag"><?= e($libro['materia']) ?></span>
            <?php endif; ?>
          </div>
          <div class="libro-card-footer">
            <span class="libro-disponibilidad <?= $libro['cantidad_disponible'] > 0 ? 'disp-si' : 'disp-no' ?>">
              <?= $libro['cantidad_disponible'] > 0
                ? $libro['cantidad_disponible'] . ' de ' . $libro['cantidad_total'] . ' disponibles'
                : 'Sin ejemplares disponibles' ?>
            </span>
            <a href="libro.php?id=<?= $libro['id'] ?>" class="btn-reservar" style="text-decoration:none; display:inline-block;">
              Ver detalle
            </a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/footer.php'; ?>
