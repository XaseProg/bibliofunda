<?php
require_once __DIR__ . '/functions.php';
iniciar_sesion();

if (es_admin()) {
    redirect('admin/dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $password = $_POST['password'] ?? '';
    if (login_admin($usuario, $password)) {
        redirect('admin/dashboard.php');
    } else {
        $error = 'Usuario o contraseña incorrectos.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panel de administración - Biblioteca ISFT N° 240</title>
  <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
  <div class="login-pagina">
    <div class="login-card">
      <div class="login-logo">
        <svg viewBox="0 0 24 24" fill="none" stroke="var(--azul-700)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin: 0 auto;">
          <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
          <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
        </svg>
        <h2>Panel de Administración</h2>
        <p>Biblioteca ISFT N° 240</p>
      </div>
      <?php if ($error): ?>
        <div class="mensaje mensaje-error"><?= e($error) ?></div>
      <?php endif; ?>
      <form method="POST" action="login.php">
        <div class="form-grupo">
          <label for="usuario">Usuario</label>
          <input type="text" id="usuario" name="usuario" required autofocus>
        </div>
        <div class="form-grupo">
          <label for="password">Contraseña</label>
          <input type="password" id="password" name="password" required>
        </div>
        <div class="form-acciones">
          <button type="submit" class="btn btn-primario" style="width:100%; justify-content:center;">Ingresar</button>
        </div>
      </form>
      <div style="text-align:center; margin-top:1.5rem;">
        <a href="index.php" style="color: var(--azul-700); font-size: 0.9rem;">&larr; Volver al catálogo</a>
      </div>
    </div>
  </div>
</body>
</html>
