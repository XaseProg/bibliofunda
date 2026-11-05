<?php
// ============================================================
// Configuración de la base de datos
// ============================================================
// Modificá estos valores con los datos de tu servidor MySQL

define('DB_HOST', 'localhost');
define('DB_NAME', 'biblioteca_isft240');
define('DB_USER', 'root');       // tu usuario de MySQL
define('DB_PASS', '');           // tu contraseña de MySQL

// ============================================================
// Conexión a la base de datos (PDO)
// ============================================================
function db() {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die('Error de conexión a la base de datos: ' . htmlspecialchars($e->getMessage()));
        }
    }
    return $pdo;
}
