<?php
require_once __DIR__ . '/../functions.php';
requerir_admin();

// Estadísticas
$total_libros = db()->query('SELECT COUNT(*) FROM libros')->fetchColumn();
$total_ejemplares = db()->query('SELECT COALESCE(SUM(cantidad_total),0) FROM libros')->fetchColumn();
$disponibles = db()->query('SELECT COALESCE(SUM(cantidad_disponible),0) FROM libros')->fetchColumn();
$reservados = db()->query("SELECT COUNT(*) FROM reservas WHERE estado='reservado'")->fetchColumn();
$prestados = db()->query("SELECT COUNT(*) FROM reservas WHERE estado='prestado'")->fetchColumn();
$devueltos = db()->query("SELECT COUNT(*) FROM reservas WHERE estado='devuelto'")->fetchColumn();

// Préstamos próximos a vencer
$stmt = db()->query("SELECT r.*, l.titulo, l.autor FROM reservas r
    JOIN libros l ON r.libro_id = l.id
    WHERE r.estado = 'prestado' AND r.fecha_devolucion < DATE_ADD(NOW(), INTERVAL 3 DAY)
    ORDER BY r.fecha_devolucion ASC");
$por_vencer = $stmt->fetchAll();

include 'header_admin.php';
?>

<div class="contenedor">
  <div class="seccion-titulo">
    <h2>Resumen general</h2>
  </div>

  <div class="dashboard-grid">
    <div class="stat-card">
      <div class="stat-icono stat-azul">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
        </svg>
      </div>
      <div class="stat-info"><h3><?= (int)$total_libros ?></h3><p>Libros registrados</p></div>
    </div>
    <div class="stat-card">
      <div class="stat-icono stat-verde">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
        </svg>
      </div>
      <div class="stat-info"><h3><?= (int)$disponibles ?></h3><p>Ejemplares disponibles</p></div>
    </div>
    <div class="stat-card">
      <div class="stat-icono stat-amarillo">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
        </svg>
      </div>
      <div class="stat-info"><h3><?= (int)$reservados ?></h3><p>Reservados</p></div>
    </div>
    <div class="stat-card">
      <div class="stat-icono stat-rojo">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M16 2v6h6"/><path d="M22 8 16 2"/><path d="M21 19V8H3v11a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2z"/>
        </svg>
      </div>
      <div class="stat-info"><h3><?= (int)$prestados ?></h3><p>Prestados</p></div>
    </div>
  </div>

  <?php if (!empty($por_vencer)): ?>
    <div class="seccion-titulo">
      <h2>Préstamos próximos a vencer</h2>
    </div>
    <div class="tabla-contenedor">
      <table class="tabla">
        <thead>
          <tr><th>Libro</th><th>Prestado a</th><th>Teléfono</th><th>Fecha devolución</th></tr>
        </thead>
        <tbody>
          <?php foreach ($por_vencer as $r): ?>
            <tr>
              <td><strong><?= e($r['titulo']) ?></strong><br><small><?= e($r['autor']) ?></small></td>
              <td><?= e($r['nombre_completo']) ?></td>
              <td><?= e($r['telefono']) ?></td>
              <td><?= e(date('d/m/Y', strtotime($r['fecha_devolucion']))) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>

  <div class="seccion-titulo" style="margin-top: 2rem;">
    <h2>Acciones rápidas</h2>
  </div>
  <div style="display:flex; gap:1rem; flex-wrap:wrap;">
    <a href="libro_form.php" class="btn btn-primario">+ Cargar nuevo libro</a>
    <a href="reservas.php" class="btn btn-secundario">Ver reservas y préstamos</a>
    <a href="libros.php" class="btn btn-secundario">Gestionar libros</a>
  </div>
</div>

<?php include 'footer_admin.php'; ?>
