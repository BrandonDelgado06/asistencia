<?php
require_once __DIR__ . '/../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id > 0) {
        $st = $conn->prepare("UPDATE empleados SET estado = IF(estado = 1, 0, 1) WHERE id = ?");
        $st->bind_param('i', $id);
        $st->execute();
        $_SESSION['msg'] = 'Estado del empleado actualizado.';
    }
}
header('Location: empleados.php');
exit;