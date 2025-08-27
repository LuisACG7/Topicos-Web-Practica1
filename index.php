<?php
require_once __DIR__ . '/config.php';

$mensaje_ok = '';
$mensaje_err = '';

try {
    $conexion = obtener_conexion();
} catch (Throwable $e) {
    http_response_code(500);
    echo "<h1>Error de conexión.</h1><pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    try {
        if ($accion === 'crear') {
            $nombre = trim($_POST['first_name'] ?? '');
            $apellido = trim($_POST['last_name'] ?? '');
            if ($nombre === '' || $apellido === '') {
                throw new RuntimeException('Nombre y apellido son obligatorios.');
            }
            $sql_crear = "INSERT INTO actor (first_name, last_name, last_update) VALUES (:nombre, :apellido, NOW())";
            $stmt = $conexion->prepare($sql_crear);
            $stmt->bindValue(':nombre', $nombre);
            $stmt->bindValue(':apellido', $apellido);
            $stmt->execute();
            $mensaje_ok = 'Actor creado correctamente.';
        } elseif ($accion === 'actualizar') {
            $actor_id = (int)($_POST['actor_id'] ?? 0);
            $nombre = trim($_POST['first_name'] ?? '');
            $apellido = trim($_POST['last_name'] ?? '');
            if ($actor_id <= 0 || $nombre === '' || $apellido === '') {
                throw new RuntimeException('ID, nombre y apellido son obligatorios.');
            }
            $sql_actualizar = "UPDATE actor SET first_name = :nombre, last_name = :apellido, last_update = NOW() WHERE actor_id = :id";
            $stmt = $conexion->prepare($sql_actualizar);
            $stmt->bindValue(':nombre', $nombre);
            $stmt->bindValue(':apellido', $apellido);
            $stmt->bindValue(':id', $actor_id, PDO::PARAM_INT);
            $stmt->execute();
            $mensaje_ok = 'Actor actualizado correctamente.';
        } elseif ($accion === 'eliminar') {
            $actor_id = (int)($_POST['actor_id'] ?? 0);
            if ($actor_id <= 0) {
                throw new RuntimeException('ID inválido.');
            }
            $sql_eliminar = "DELETE FROM actor WHERE actor_id = :id";
            $stmt = $conexion->prepare($sql_eliminar);
            $stmt->bindValue(':id', $actor_id, PDO::PARAM_INT);
            $stmt->execute();
            $mensaje_ok = 'Actor eliminado correctamente.';
        }
    } catch (Throwable $e) {
        $mensaje_err = $e->getMessage();
    }
}

try {
    $sql_listar = "SELECT actor_id, first_name, last_name, last_update FROM actor ORDER BY actor_id ASC ";
    $actores = $conexion->query($sql_listar)->fetchAll();
} catch (Throwable $e) {
    http_response_code(500);
    echo "<h1>Error listando actores.</h1><pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CRUD Sakila — Actores</title>
  <style>
    :root { --borde:#e5e7eb; --fondo:#f8fafc; --texto:#0f172a; --muted:#64748b; --prim:#2563eb; --prim2:#1d4ed8; --ok:#16a34a; --err:#dc2626; }
    * { box-sizing: border-box; }
    body { font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; background: var(--fondo); color: var(--texto); margin: 0; }
    .contenedor { max-width: 1100px; margin: 32px auto; padding: 0 16px; }
    h1 { font-size: 28px; margin: 0 0 12px; }
    .sub { color: var(--muted); margin-bottom: 20px; }
    .tarjeta { background: white; border: 1px solid var(--borde); border-radius: 16px; padding: 16px; box-shadow: 0 6px 20px rgba(15,23,42,.05); }
    .fila { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .btn { background: var(--prim); color: white; border: none; border-radius: 10px; padding: 10px 14px; cursor: pointer; font-weight: 600; }
    .btn:hover { background: var(--prim2); }
    .btn-sec { background: white; color: var(--texto); border: 1px solid var(--borde); border-radius: 10px; padding: 10px 14px; cursor: pointer; }
    .btn-sec:hover { background: #f1f5f9; }
    .btn-elim { background: var(--err); color:white; border:none; border-radius:10px; padding:10px 12px; cursor:pointer; }
    .btn-elim:hover { filter: brightness(.95); }
    .tabla { width: 100%; border-collapse: collapse; margin-top: 20px; }
    .tabla th, .tabla td { border-bottom: 1px solid var(--borde); padding: 10px; text-align: left; }
    .tabla th { font-weight: 700; color: var(--muted); font-size: 12px; text-transform: uppercase; letter-spacing: .06em; }
    .grupo { display: flex; gap: 8px; align-items: center; }
    input[type="text"] { width: 100%; border: 1px solid var(--borde); border-radius: 10px; padding: 10px 12px; font-size: 14px; }
    .alerta { margin-top: 12px; padding: 10px 12px; border-radius: 10px; font-size: 14px; }
    .ok { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
    .err { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
    .acciones { display: flex; gap: 8px; }
    .sticky { position: sticky; top: 0; background:white; }
    .id { font-variant-numeric: tabular-nums; opacity: .8; }
    .nota { color: var(--muted); font-size: 13px; margin-top: 6px; }
    @media (max-width: 780px) { .fila { grid-template-columns: 1fr; } .acciones { flex-direction: column; } }
  </style>
  <script>
    function confirmarEliminar(actorId) {
      return confirm('¿Seguro que deseas eliminar al actor con ID ' + actorId + '?');
    }
  </script>
</head>
<body>
  <div class="contenedor">
    <h1>CRUD de Actores — Sakila</h1>
    <div class="sub">Gestiona actores: Crear, Editar, y Eliminar.</div>

    <?php if ($mensaje_ok !== ''): ?>
      <div class="alerta ok"><?= htmlspecialchars($mensaje_ok) ?></div>
    <?php endif; ?>

    <?php if ($mensaje_err !== ''): ?>
      <div class="alerta err"><?= htmlspecialchars($mensaje_err) ?></div>
    <?php endif; ?>

    <div class="tarjeta" style="margin-top:16px;">
      <h2 style="margin-top:0;">Agregar actor</h2>
      <form method="post" class="fila">
        <input type="hidden" name="accion" value="crear">
        <div>
          <label for="crear_nombre" style="font-weight:600;">Nombre</label>
          <input id="crear_nombre" name="first_name" type="text" placeholder="Nombre..." required>
        </div>
        <div>
          <label for="crear_apellido" style="font-weight:600;">Apellido</label>
          <input id="crear_apellido" name="last_name" type="text" placeholder="Apellido..." required>
        </div>
        <div style="grid-column: 1 / -1; display:flex; justify-content:flex-end;">
          <button class="btn" type="submit">Crear actor</button>
        </div>
      </form>
    </div>

    <div class="tarjeta" style="margin-top:16px;">
      <h2 style="margin-top:0;">Lista de Actores</h2>
      <table class="tabla">
        <thead class="sticky">
          <tr>
            <th style="width:100px;">ID</th>
            <th>Nombre</th>
            <th>Apellido</th>
            <th style="width:220px;">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($actores as $fila): ?>
            <tr>
              <td class="id"><?= htmlspecialchars((string)$fila['actor_id']) ?></td>
              <td>
                <form method="post" class="grupo" style="margin:0;">
                  <input type="hidden" name="accion" value="actualizar">
                  <input type="hidden" name="actor_id" value="<?= htmlspecialchars((string)$fila['actor_id']) ?>">
                  <input type="text" name="first_name" value="<?= htmlspecialchars($fila['first_name']) ?>" required>
              </td>
              <td>
                  <input type="text" name="last_name" value="<?= htmlspecialchars($fila['last_name']) ?>" required>
              </td>
              <td class="acciones">
                  <button class="btn-sec" type="submit" title="Guardar cambios">Guardar</button>
                </form>
                <form method="post" onsubmit="return confirmarEliminar(<?= (int)$fila['actor_id'] ?>);" style="margin:0;">
                  <input type="hidden" name="accion" value="eliminar">
                  <input type="hidden" name="actor_id" value="<?= htmlspecialchars((string)$fila['actor_id']) ?>">
                  <button class="btn-elim" type="submit" title="Eliminar actor">Eliminar</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <div class="nota">El ID no es editable; solo puedes cambiar nombre y apellido o eliminar el registro.</div>
    </div>
  </div>
</body>
</html>
