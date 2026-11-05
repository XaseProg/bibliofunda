<?php
require_once __DIR__ . '/../functions.php';
requerir_admin();

$id = (int)($_GET['id'] ?? 0);
if ($id) {
    eliminar_libro($id);
}
redirect('libros.php?msg=eliminado');
