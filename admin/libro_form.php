<?php
require_once __DIR__ . '/../functions.php';
requerir_admin();

$id = (int)($_GET['id'] ?? 0);
$libro = $id ? obtener_libro($id) : null;
$es_edicion = (bool)$libro;

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'titulo' => trim($_POST['titulo'] ?? ''),
        'autor' => trim($_POST['autor'] ?? ''),
        'carrera' => trim($_POST['carrera'] ?? ''),
        'curso' => trim($_POST['curso'] ?? ''),
        'materia' => trim($_POST['materia'] ?? ''),
        'cantidad_total' => (int)($_POST['cantidad_total'] ?? 1),
    ];

    if ($datos['titulo'] === '' || $datos['autor'] === '') {
        $error = 'El título y el autor son obligatorios.';
    } elseif ($datos['cantidad_total'] < 1) {
        $error = 'La cantidad de ejemplares debe ser al menos 1.';
    } else {
        if ($es_edicion) {
            actualizar_libro($id, $datos);
        } else {
            crear_libro($datos);
        }
        redirect('libros.php?msg=guardado');
    }
}

include 'header_admin.php';
?>

<div class="contenedor-estrecho">
  <div class="seccion-titulo">
    <h2><?= $es_edicion ? 'Editar libro' : 'Cargar nuevo libro' ?></h2>
  </div>

  <?php if ($error): ?>
    <div class="mensaje mensaje-error"><?= e($error) ?></div>
  <?php endif; ?>

  <div class="form-card">
    <form method="POST" action="<?= $es_edicion ? 'libro_form.php?id=' . $id : 'libro_form.php' ?>">
      <div class="form-grupo">
        <label for="titulo">Título *</label>
        <input type="text" id="titulo" name="titulo" required value="<?= e($libro['titulo'] ?? '') ?>">
      </div>
      <div class="form-grupo">
        <label for="autor">Autor *</label>
        <input type="text" id="autor" name="autor" required value="<?= e($libro['autor'] ?? '') ?>">
      </div>
      <div class="form-grupo">
        <label for="carrera">Carrera</label>
        <input type="text" id="carrera" name="carrera" placeholder="Ej: Tecnicatura en Sistemas" value="<?= e($libro['carrera'] ?? '') ?>">
      </div>
      <div class="form-grupo">
        <label for="curso">Curso</label>
        <input type="text" id="curso" name="curso" placeholder="Ej: 1er año" value="<?= e($libro['curso'] ?? '') ?>">
      </div>
      <div class="form-grupo">
        <label for="materia">Materia</label>
        <input type="text" id="materia" name="materia" placeholder="Ej: Programación I" value="<?= e($libro['materia'] ?? '') ?>">
      </div>
      <div class="form-grupo">
        <label for="cantidad_total">Cantidad de ejemplares *</label>
        <input type="number" id="cantidad_total" name="cantidad_total" min="1" required value="<?= e($libro['cantidad_total'] ?? 1) ?>">
      </div>
      <div class="form-acciones">
        <button type="submit" class="btn btn-primario"><?= $es_edicion ? 'Guardar cambios' : 'Cargar libro' ?></button>
        <a href="libros.php" class="btn btn-secundario">Cancelar</a>
      </div>
    </form>
  </div>
</div>

<?php include 'footer_admin.php'; ?>
