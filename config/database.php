<?php
// Conexión a MySQL con mysqli
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'control_asistencia';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $conn = new mysqli($host, $user, $pass, $db);
    $conn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    die('Error de conexión: ' . $e->getMessage());
}
date_default_timezone_set('America/Costa_Rica');
$conn->query("SET time_zone = '-06:00'");