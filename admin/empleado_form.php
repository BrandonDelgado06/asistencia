<?php
require_once __DIR__ . '/../includes/auth.php';

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$emp = ['cedula' => '', 'nombre' => '', 'correo' => '', 'telefono' => '', 'codigo_acceso' => '', 'estado' => 1];
$errores = [];

if ($id > 0) {
    $st = $conn->prepare("SELECT * FROM empleados WHERE id = ?");
    $st->bind_param('i', $id);
    $st->execute();
    $fila = $st->get_result()->fetch_assoc();
    if (!$fila) { header('Location: empleados.php'); exit; }
    $emp = $fila;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emp['cedula']   = limpiar($_POST['cedula'] ?? '');
    $emp['nombre']   = limpiar($_POST['nombre'] ?? '');
    $emp['correo']   = limpiar($_POST['correo'] ?? '');
    $emp['telefono'] = limpiar($_POST['telefono'] ?? '');
    $emp['estado']   = isset($_POST['estado']) ? 1 : 0;

    // Validaciones
    if ($emp['cedula'] === '' || $emp['nombre'] === '' || $emp['correo'] === '' || $emp['telefono'] === '') {
        $errores[] = 'Todos los campos son obligatorios.';
    }
    if ($emp['cedula'] !== '' && !preg_match('/^[0-9\-]{5,20}$/', $emp['cedula'])) {
        $errores[] = 'La cédula solo puede contener números y guiones (5 a 20 caracteres).';
    }
    if ($emp['correo'] !== '' && !filter_var($emp['correo'], FILTER_VALIDATE_EMAIL)) {
        $errores[] = 'El correo electrónico no es válido.';
    }
    if ($emp['telefono'] !== '' && !preg_match('/^[0-9\-\+\s]{7,20}$/', $emp['telefono'])) {
        $errores[] = 'El teléfono no es válido.';
    }
    // Cédula repetida
    if (!$errores) {
        $st = $conn->prepare("SELECT id FROM empleados WHERE cedula = ? AND id <> ?");
        $st->bind_param('si', $emp['cedula'], $id);
        $st->execute();
        if ($st->get_result()->num_rows > 0) $errores[] = 'Ya existe un empleado con esa cédula.';
    }

    if (!$errores) {
        if ($id > 0) {
            $st = $conn->prepare("UPDATE empleados SET cedula=?, nombre=?, correo=?, telefono=?, estado=? WHERE id=?");
            $st->bind_param('ssssii', $emp['cedula'], $emp['nombre'], $emp['correo'], $emp['telefono'], $emp['estado'], $id);
            $st->execute();
            $_SESSION['msg'] = 'Empleado actualizado correctamente.';
        } else {
            $codigo = generarCodigo($conn);
            $st = $conn->prepare("INSERT INTO empleados (cedula, nombre, correo, telefono, codigo_acceso, estado) VALUES (?,?,?,?,?,?)");
            $st->bind_param('sssssi', $emp['cedula'], $emp['nombre'], $emp['correo'], $emp['telefono'], $codigo, $emp['estado']);
            $st->execute();
            $_SESSION['msg'] = "Empleado registrado. Código de acceso generado: $codigo";
        }
        header('Location: empleados.php');
        exit;
    }
}

include __DIR__ . '/../includes/header.php';
?>
<h3 class="mb-3"><?= $id ? 'Editar empleado' : 'Registrar nuevo empleado' ?></h3>
<?php foreach ($errores as $er): ?><div class="alert alert-danger py-2"><?= e($er) ?></div><?php endforeach; ?>

<div class="card shadow-sm">
  <div class="card-body">
    <form method="post" novalidate>
      <input type="hidden" name="id" value="<?= $id ?>">
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Cédula *</label>
          <input type="text" name="cedula" class="form-control" value="<?= e($emp['cedula']) ?>" placeholder="1-1111-1111" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Nombre completo *</label>
          <input type="text" name="nombre" class="form-control" value="<?= e($emp['nombre']) ?>" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Correo *</label>
          <input type="email" name="correo" class="form-control" value="<?= e($emp['correo']) ?>" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Teléfono *</label>
          <input type="text" name="telefono" class="form-control" value="<?= e($emp['telefono']) ?>" required>
        </div>
        <?php if ($id): ?>
        <div class="col-md-6">
          <label class="form-label">Código de acceso</label>
          <input type="text" class="form-control codigo" value="<?= e($emp['codigo_acceso']) ?>" disabled>
        </div>
        <?php else: ?>
        <div class="col-md-6"><div class="form-text mt-4">El código de acceso se genera automáticamente al guardar.</div></div>
        <?php endif; ?>
        <div class="col-12">
          <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="estado" id="estado" <?= $emp['estado'] ? 'checked' : '' ?>>
            <label class="form-check-label" for="estado">Empleado activo</label>
          </div>
        </div>
      </div>
      <div class="mt-4">
        <button class="btn btn-primary">Guardar</button>
        <a href="empleados.php" class="btn btn-outline-secondary">Cancelar</a>
      </div>
    </form>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>