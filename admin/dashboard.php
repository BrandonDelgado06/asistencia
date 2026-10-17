<?php
require_once __DIR__ . '/../includes/auth.php';

function contar($conn, $sql) { return (int)$conn->query($sql)->fetch_row()[0]; }
$total   = contar($conn, "SELECT COUNT(*) FROM empleados");
$activos = contar($conn, "SELECT COUNT(*) FROM empleados WHERE estado = 1");
$hoyIng  = contar($conn, "SELECT COUNT(DISTINCT empleado_id) FROM asistencias WHERE fecha = CURDATE()");
$dentro  = contar($conn, "SELECT COUNT(*) FROM asistencias WHERE fecha = CURDATE() AND hora_salida IS NULL");
// Registros del día = entradas + salidas marcadas hoy
$regs    = contar($conn, "SELECT COUNT(*) + COALESCE(SUM(hora_salida IS NOT NULL),0) FROM asistencias WHERE fecha = CURDATE()");

$adentro = $conn->query("SELECT e.cedula, e.nombre, a.hora_entrada FROM asistencias a
    JOIN empleados e ON e.id = a.empleado_id
    WHERE a.fecha = CURDATE() AND a.hora_salida IS NULL ORDER BY a.hora_entrada");

include __DIR__ . '/../includes/header.php';
$tarjetas = [
  ['Total de empleados', $total, '#0d6efd'],
  ['Empleados activos', $activos, '#198754'],
  ['Ingresaron hoy', $hoyIng, '#fd7e14'],
  ['Actualmente dentro', $dentro, '#6f42c1'],
  ['Registros de hoy', $regs, '#dc3545'],
];
?>
<h3 class="mb-4">Dashboard</h3>
<div class="row g-3 mb-4">
  <?php foreach ($tarjetas as $t): ?>
  <div class="col-12 col-sm-6 col-lg">
    <div class="card card-stat shadow-sm" style="border-left-color:<?= $t[2] ?>">
      <div class="card-body">
        <div class="text-muted small"><?= $t[0] ?></div>
        <div class="num"><?= $t[1] ?></div>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<div class="card shadow-sm">
  <div class="card-header">Personas actualmente dentro de la empresa</div>
  <div class="table-responsive">
    <table class="table table-striped mb-0">
      <thead><tr><th>Cédula</th><th>Empleado</th><th>Hora de entrada</th></tr></thead>
      <tbody>
      <?php if ($adentro->num_rows === 0): ?>
        <tr><td colspan="3" class="text-center text-muted">Nadie se encuentra dentro en este momento.</td></tr>
      <?php endif; ?>
      <?php while ($r = $adentro->fetch_assoc()): ?>
        <tr><td><?= e($r['cedula']) ?></td><td><?= e($r['nombre']) ?></td><td><?= hora24($r['hora_entrada']) ?></td></tr>
      <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>