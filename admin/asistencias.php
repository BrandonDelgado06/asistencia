<?php
require_once __DIR__ . '/../includes/auth.php';

$empleado = limpiar($_GET['empleado'] ?? '');
$cedula   = limpiar($_GET['cedula'] ?? '');
$fecha    = limpiar($_GET['fecha'] ?? '');
$desde    = limpiar($_GET['desde'] ?? '');
$hasta    = limpiar($_GET['hasta'] ?? '');

function fechaValida($f) { $d = DateTime::createFromFormat('Y-m-d', $f); return $d && $d->format('Y-m-d') === $f; }

$sql = "SELECT a.*, e.cedula, e.nombre FROM asistencias a JOIN empleados e ON e.id = a.empleado_id WHERE 1=1";
$tipos = '';
$params = [];
if ($empleado !== '') { $sql .= " AND e.nombre LIKE ?"; $tipos .= 's'; $params[] = "%$empleado%"; }
if ($cedula !== '')   { $sql .= " AND e.cedula LIKE ?"; $tipos .= 's'; $params[] = "%$cedula%"; }
if ($fecha !== '' && fechaValida($fecha)) { $sql .= " AND a.fecha = ?"; $tipos .= 's'; $params[] = $fecha; }
if ($desde !== '' && fechaValida($desde)) { $sql .= " AND a.fecha >= ?"; $tipos .= 's'; $params[] = $desde; }
if ($hasta !== '' && fechaValida($hasta)) { $sql .= " AND a.fecha <= ?"; $tipos .= 's'; $params[] = $hasta; }
$sql .= " ORDER BY a.fecha DESC, a.hora_entrada DESC";

$st = $conn->prepare($sql);
if ($params) $st->bind_param($tipos, ...$params);
$st->execute();
$res = $st->get_result();

include __DIR__ . '/../includes/header.php';
?>
<h3 class="mb-3">Historial de asistencia</h3>

<form method="get" class="card card-body shadow-sm mb-3">
  <div class="row g-2">
    <div class="col-md-3"><label class="form-label small">Empleado</label><input type="text" name="empleado" class="form-control" value="<?= e($empleado) ?>"></div>
    <div class="col-md-2"><label class="form-label small">Cédula</label><input type="text" name="cedula" class="form-control" value="<?= e($cedula) ?>"></div>
    <div class="col-md-2"><label class="form-label small">Fecha</label><input type="date" name="fecha" class="form-control" value="<?= e($fecha) ?>"></div>
    <div class="col-md-2"><label class="form-label small">Desde</label><input type="date" name="desde" class="form-control" value="<?= e($desde) ?>"></div>
    <div class="col-md-2"><label class="form-label small">Hasta</label><input type="date" name="hasta" class="form-control" value="<?= e($hasta) ?>"></div>
    <div class="col-md-1 d-flex align-items-end gap-1">
      <button class="btn btn-primary">Filtrar</button>
    </div>
  </div>
  <div class="mt-2"><a href="asistencias.php" class="small">Limpiar filtros</a></div>
</form>

<div class="card shadow-sm">
  <div class="table-responsive">
    <table class="table table-striped align-middle mb-0">
      <thead><tr><th>Cédula</th><th>Empleado</th><th>Fecha</th><th>Entrada</th><th>Salida</th><th>Horas trabajadas</th></tr></thead>
      <tbody>
      <?php if ($res->num_rows === 0): ?>
        <tr><td colspan="6" class="text-center text-muted">No se encontraron registros.</td></tr>
      <?php endif; ?>
      <?php while ($r = $res->fetch_assoc()): ?>
        <tr>
          <td><?= e($r['cedula']) ?></td>
          <td><?= e($r['nombre']) ?></td>
          <td><?= fechaCR($r['fecha']) ?></td>
          <td><?= hora24($r['hora_entrada']) ?></td>
          <td><?= $r['hora_salida'] ? hora24($r['hora_salida']) : '<span class="badge bg-warning text-dark">Dentro</span>' ?></td>
          <td><?= horasTrabajadas($r['hora_entrada'], $r['hora_salida']) ?></td>
        </tr>
      <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>