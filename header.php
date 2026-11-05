<?php
require_once __DIR__ . '/functions.php';
iniciar_sesion();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Biblioteca ISFT N° 240</title>
  <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
  <header class="header">
    <div class="header-contenido">
      <a href="index.php" class="header-logo">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
          <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
        </svg>
        <div class="header-titulo">
          <h1>Biblioteca ISFT N° 240</h1>
          <p>Instituto Superior de Formación Técnica</p>
        </div>
      </a>
      <nav class="header-nav">
        <a href="index.php" class="<?= ($_SERVER['PHP_SELF'] ?? '') === '/index.php' ? 'activo' : '' ?>">Catálogo</a>
        <?php if (es_admin()): ?>
          <a href="admin/dashboard.php">Panel</a>
          <a href="logout.php">Cerrar sesión</a>
        <?php else: ?>
          <a href="login.php">Admin</a>
        <?php endif; ?>
      </nav>
    </div>
  </header>
