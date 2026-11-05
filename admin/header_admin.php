<?php
require_once __DIR__ . '/../functions.php';
requerir_admin();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panel - Biblioteca ISFT N° 240</title>
  <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
  <header class="header">
    <div class="header-contenido">
      <a href="dashboard.php" class="header-logo">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
          <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
        </svg>
        <div class="header-titulo">
          <h1>Panel de Administración</h1>
          <p>Biblioteca ISFT N° 240</p>
        </div>
      </a>
      <nav class="header-nav">
        <a href="dashboard.php" class="<?= basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'activo' : '' ?>">Inicio</a>
        <a href="libros.php" class="<?= basename($_SERVER['PHP_SELF']) === 'libros.php' ? 'activo' : '' ?>">Libros</a>
        <a href="reservas.php" class="<?= basename($_SERVER['PHP_SELF']) === 'reservas.php' ? 'activo' : '' ?>">Reservas y préstamos</a>
        <a href="../index.php">Ver catálogo</a>
        <a href="../logout.php">Cerrar sesión</a>
      </nav>
    </div>
  </header>
