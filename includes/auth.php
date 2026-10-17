<?php
// Protege las páginas administrativas
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/funciones.php';

if (empty($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}