<?php
require_once __DIR__ . '/../includes/auth.php';

$busca = limpiar($_GET['q'] ?? '');
if ($busca !== '') {
    $like = "%$busca%";
    $st = $conn->prepare("SELECT * FROM empleados WHERE nombre LIKE ? OR cedula LIKE ? ORDER BY nombre");
    $st->bind_param('ss', $like, $like);
    $st->execute();
    $res = $st->get_result();
} else {
    $res = $conn->query("SELECT * FROM empleados ORDER BY nombre");
}
$msg = $_SESSION['msg'] ?? '';
unset($_SESSION['msg']);

include __DIR__ . '/../includes/header.php';
?>
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
  <h3 class="mb-0">Empleados</h3>
  <a href="empleado_form.php" class="btn btn-primary">+ Nuevo empleado</a>
</div>
<?php if ($msg): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>

<form class="row g-2 mb-3" method="get">
  <div class="col-8 col-md-4"><input type="text" name="q" class="form-control" placeholder="Buscar por nombre o cédula" value="<?= e($busca) ?>"></div>
  <div class="col-auto"><button class="btn btn-outline-primary">Buscar</button></div>
</form>

<div class="card shadow-sm">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead><tr><th>Cédula</th><th>Nombre</th><th>Correo</th><th>Teléfono</th><th>Código</th><th>Estado</th><th>Registro</th><th class="text-end">Acciones</th></tr></thead>
      <tbody>
      <?php if ($res->num_rows === 0): ?>
        <tr><td colspan="8" class="text-center text-muted">No hay empleados.</td></tr>
      <?php endif; ?>
      <?php while ($r = $res->fetch_assoc()): ?>
        <tr>
          <td><?= e($r['cedula']) ?></td>
          <td><?= e($r['nombre']) ?></td>
          <td><?= e($r['correo']) ?></td>
          <td><?= e($r['telefono']) ?></td>
          <td class="codigo"><?= e($r['codigo_acceso']) ?></td>
          <td><span class="badge bg-<?= $r['estado'] ? 'success' : 'secondary' ?>"><?= $r['estado'] ? 'Activo' : 'Inactivo' ?></span></td>
          <td><?= fechaCR($r['fecha_registro']) ?></td>
          <td class="text-end text-nowrap">
            <a class="btn btn-sm btn-outline-primary" href="empleado_form.php?id=<?= (int)$r['id'] ?>">Editar</a>
            <form method="post" action="empleado_estado.php" class="d-inline">
              <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
              <button class="btn btn-sm btn-outline-<?= $r['estado'] ? 'danger' : 'success' ?> js-confirmar"
                      data-msg="¿<?= $r['estado'] ? 'Desactivar' : 'Activar' ?> a este empleado?">
                <?= $r['estado'] ? 'Desactivar' : 'Activar' ?>
              </button>
            </form>
          </td>
        </tr>
      <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>