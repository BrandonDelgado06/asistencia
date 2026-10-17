<?php
$pagina = basename($_SERVER['PHP_SELF']);
function activo($p, $a) { return $p === $a ? 'active' : ''; }
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Control de Asistencia</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/style.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container">
    <a class="navbar-brand" href="dashboard.php">Control de Asistencia</a>
    <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#menu"><span class="navbar-toggler-icon"></span></button>
    <div id="menu" class="collapse navbar-collapse">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link <?= activo($pagina,'dashboard.php') ?>" href="dashboard.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link <?= activo($pagina,'empleados.php') ?>" href="empleados.php">Empleados</a></li>
        <li class="nav-item"><a class="nav-link <?= activo($pagina,'empleado_form.php') ?>" href="empleado_form.php">Nuevo empleado</a></li>
        <li class="nav-item"><a class="nav-link <?= activo($pagina,'asistencias.php') ?>" href="asistencias.php">Historial</a></li>
        <li class="nav-item"><a class="nav-link" href="../index.php" target="_blank">Pantalla de marcación</a></li>
      </ul>
      <span class="navbar-text me-3"><?= e($_SESSION['usuario_nombre'] ?? '') ?></span>
      <a class="btn btn-outline-light btn-sm" href="logout.php">Cerrar sesión</a>
    </div>
  </div>
</nav>
<main class="container py-4">