<?php
require_once __DIR__ . '/../functions.php';
requerir_admin();

$filtro = $_GET['estado'] ?? '';
$reservas = obtener_reservas($filtro);

include 'header_admin.php';
?>

<div class="contenedor">
  <div class="seccion-titulo">
    <h2>Reservas y préstamos</h2>
    <a href="prestar.php" class="btn btn-primario">+ Nuevo préstamo</a>
  </div>

  <div class="filtros">
    <a href="reservas.php" class="filtro-btn <?= $filtro === '' ? 'activo' : '' ?>">Todos</a>
    <a href="reservas.php?estado=reservado" class="filtro-btn <?= $filtro === 'reservado' ? 'activo' : '' ?>">Reservados</a>
    <a href="reservas.php?estado=prestado" class="filtro-btn <?= $filtro === 'prestado' ? 'activo' : '' ?>">Prestados</a>
    <a href="reservas.php?estado=devuelto" class="filtro-btn <?= $filtro === 'devuelto' ? 'activo' : '' ?>">Devueltos</a>
    <a href="reservas.php?estado=cancelado" class="filtro-btn <?= $filtro === 'cancelado' ? 'activo' : '' ?>">Cancelados</a>
  </div>

  <?php if (empty($reservas)): ?>
    <div class="vacio">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
        <path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
      </svg>
      <h3>No hay registros</h3>
      <p>No se encontraron reservas o préstamos con este filtro.</p>
    </div>
  <?php else: ?>
    <div class="tabla-contenedor">
      <table class="tabla">
        <thead>
          <tr>
            <th>Libro</th><th>Persona</th><th>Teléfono</th><th>Carrera/Curso</th>
            <th>Estado</th><th>Reserva</th><th>Préstamo</th><th>Devolver</th><th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($reservas as $r): ?>
            <tr>
              <td><strong><?= e($r['titulo']) ?></strong><br><small><?= e($r['autor']) ?></small></td>
              <td><?= e($r['nombre_completo']) ?></td>
              <td><?= e($r['telefono']) ?></td>
              <td><?= e($r['carrera_curso']) ?></td>
              <td><span class="badge badge-<?= $r['estado'] ?>"><?= e($r['estado']) ?></span></td>
              <td><?= $r['fecha_reserva'] ? e(date('d/m/Y', strtotime($r['fecha_reserva']))) : '-' ?></td>
              <td><?= $r['fecha_prestamo'] ? e(date('d/m/Y', strtotime($r['fecha_prestamo']))) : '-' ?></td>
              <td><?= $r['fecha_devolucion'] ? e(date('d/m/Y', strtotime($r['fecha_devolucion']))) : '-' ?></td>
              <td class="tabla-acciones">
                <?php if ($r['estado'] === 'reservado'): ?>
                  <a href="prestar.php?reserva_id=<?= $r['id'] ?>" class="btn btn-primario btn-pequeno">Prestar</a>
                  <a href="cancelar.php?id=<?= $r['id'] ?>" class="btn btn-rojo btn-pequeno"
                     onclick="return confirm('¿Cancelar esta reserva?')">Cancelar</a>
                <?php elseif ($r['estado'] === 'prestado'): ?>
                  <a href="devolver.php?id=<?= $r['id'] ?>" class="btn btn-verde btn-pequeno"
                     onclick="return confirm('¿Marcar como devuelto?')">Devolver</a>
                <?php else: ?>
                  <span style="color:var(--gris-500); font-size:0.85rem;">-</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<?php include 'footer_admin.php'; ?>
