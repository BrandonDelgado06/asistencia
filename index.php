<?php
// Pantalla pública de marcación (entrada / salida)
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/funciones.php';

$tipo = '';      // 'success' | 'danger'
$mensaje = '';
$detalle = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cedula = limpiar($_POST['cedula'] ?? '');
    $codigo = strtoupper(limpiar($_POST['codigo'] ?? ''));

    if ($cedula === '' || $codigo === '') {
        $tipo = 'danger';
        $mensaje = 'La cédula y el código de acceso son obligatorios.';
    } else {
        $st = $conn->prepare("SELECT id, nombre, estado FROM empleados WHERE cedula = ? AND codigo_acceso = ?");
        $st->bind_param('ss', $cedula, $codigo);
        $st->execute();
        $emp = $st->get_result()->fetch_assoc();

        if (!$emp) {
            $tipo = 'danger';
            $mensaje = 'Cédula o código de acceso incorrecto.';
        } elseif ((int)$emp['estado'] !== 1) {
            $tipo = 'danger';
            $mensaje = 'El empleado se encuentra inactivo.';
        } else {
            $conn->begin_transaction();
            // Busca entrada abierta del día (bloquea la fila para evitar doble marcación)
            $st = $conn->prepare("SELECT id FROM asistencias WHERE empleado_id = ? AND fecha = CURDATE() AND hora_salida IS NULL ORDER BY id DESC LIMIT 1 FOR UPDATE");
            $st->bind_param('i', $emp['id']);
            $st->execute();
            $abierta = $st->get_result()->fetch_assoc();

            if ($abierta) {
                $st = $conn->prepare("UPDATE asistencias SET hora_salida = CURTIME() WHERE id = ?");
                $st->bind_param('i', $abierta['id']);
                $st->execute();
                $accion = 'salida';
            } else {
                $st = $conn->prepare("INSERT INTO asistencias (empleado_id, fecha, hora_entrada) VALUES (?, CURDATE(), CURTIME())");
                $st->bind_param('i', $emp['id']);
                $st->execute();
                $accion = 'entrada';
            }
            $conn->commit();

            $tipo = 'success';
            $detalle = [
                'nombre' => $emp['nombre'],
                'fecha'  => date('d/m/Y'),
                'hora'   => date('h:i A'),
                'accion' => $accion,
            ];
            $mensaje = ($accion === 'entrada' ? 'Entrada' : 'Salida') . ' registrada correctamente.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Registrar asistencia</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/style.css" rel="stylesheet">
</head>
<body class="marcar-wrap">
<div class="container">
  <div class="row justify-content-center">
    <div class="col-11 col-sm-9 col-md-6 col-lg-5">
      <div class="card shadow-lg">
        <div class="card-body p-4">
          <h4 class="text-center">Registro de asistencia</h4>
          <div class="text-center text-muted"><?= date('d/m/Y') ?></div>
          <div class="text-center reloj mb-3" id="reloj">--:--:--</div>

          <?php if ($mensaje): ?>
            <div class="alert alert-<?= $tipo ?>">
              <?php if ($detalle): ?>
                <div><strong>Empleado:</strong> <?= e($detalle['nombre']) ?></div>
                <div><strong>Fecha:</strong> <?= e($detalle['fecha']) ?></div>
                <div><strong>Hora de <?= $detalle['accion'] ?>:</strong> <?= e($detalle['hora']) ?></div>
                <hr class="my-2">
              <?php endif; ?>
              <?= e($mensaje) ?>
            </div>
          <?php endif; ?>

          <form method="post" autocomplete="off" novalidate>
            <div class="mb-3">
              <label class="form-label">Cédula</label>
              <input type="text" name="cedula" class="form-control form-control-lg" required autofocus>
            </div>
            <div class="mb-3">
              <label class="form-label">Código de acceso</label>
              <input type="text" name="codigo" id="codigo" class="form-control form-control-lg codigo" maxlength="6" required>
            </div>
            <button class="btn btn-primary btn-lg w-100">Registrar</button>
          </form>
          <p class="text-center mt-3 mb-0"><a href="admin/login.php" class="small">Acceso administrativo</a></p>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
  function tick() { document.getElementById('reloj').textContent = new Date().toLocaleTimeString('es-CR'); }
  tick(); setInterval(tick, 1000);
  var cod = document.getElementById('codigo');
  cod.addEventListener('input', function () { cod.value = cod.value.toUpperCase(); });
  // Al registrar correctamente, limpia el formulario tras unos segundos
  <?php if ($tipo === 'success'): ?>setTimeout(function () { window.location.href = 'index.php'; }, 6000);<?php endif; ?>
</script>
</body>
</html>