<?php
require_once __DIR__ . '/../functions.php';
requerir_admin();

$id = (int)($_GET['id'] ?? 0);
if ($id) {
    cancelar_reserva($id);
}
redirect('reservas.php?msg=cancelado');
