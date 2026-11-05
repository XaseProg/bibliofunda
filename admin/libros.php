<?php
require_once __DIR__ . '/../functions.php';
requerir_admin();

$busqueda = $_GET['q'] ?? '';
$libros = obtener_libros($busqueda, '');

include 'header_admin.php';
?>

<div class="contenedor">
  <div class="seccion-titulo">
    <h2>Gestión de libros</h2>
    <a href="libro_form.php" class="btn btn-primario">+ Cargar nuevo libro</a>
  </div>

  <div class="buscador">
    <form class="buscador-form" method="GET" action="libros.php">
      <input type="text" name="q" class="buscador-input" placeholder="Buscar por título, autor, carrera, curso o materia..." value="<?= e($busqueda) ?>">
      <button type="submit" class="btn-buscar">Buscar</button>
    </form>
  </div>

  <?php if (empty($libros)): ?>
    <div class="vacio">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
      </svg>
      <h3>No hay libros cargados</h3>
      <p>Hacé clic en "Cargar nuevo libro" para agregar el primero.</p>
    </div>
  <?php else: ?>
    <div class="tabla-contenedor">
      <table class="tabla">
        <thead>
          <tr>
            <th>Título</th><th>Autor</th><th>Carrera</th><th>Curso</th><th>Materia</th>
            <th>Disponibles</th><th>Total</th><th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($libros as $libro): ?>
            <tr>
              <td><strong><?= e($libro['titulo']) ?></strong></td>
              <td><?= e($libro['autor']) ?></td>
              <td><?= e($libro['carrera'] ?: '-') ?></td>
              <td><?= e($libro['curso'] ?: '-') ?></td>
              <td><?= e($libro['materia'] ?: '-') ?></td>
              <td><span class="<?= $libro['cantidad_disponible'] > 0 ? 'disp-si' : 'disp-no' ?>"><?= (int)$libro['cantidad_disponible'] ?></span></td>
              <td><?= (int)$libro['cantidad_total'] ?></td>
              <td class="tabla-acciones">
                <a href="libro_form.php?id=<?= $libro['id'] ?>" class="btn btn-secundario btn-pequeno">Editar</a>
                <a href="prestar.php?libro_id=<?= $libro['id'] ?>" class="btn btn-primario btn-pequeno">Prestar</a>
                <a href="libro_eliminar.php?id=<?= $libro['id'] ?>" class="btn btn-rojo btn-pequeno"
                   onclick="return confirm('¿Eliminar el libro \"<?= e($libro['titulo']) ?>\"? Esta acción no se puede deshacer.');">Eliminar</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<?php include 'footer_admin.php'; ?>
