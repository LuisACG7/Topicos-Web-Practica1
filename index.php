<?php
// index.php.
// Lista registros de la tabla actor y muestra formulario para actualizar el apellido (last_name) de cada actor.

require_once __DIR__ . '/config.php'; // Incluye la función obtener_conexion desde config.php.

// Define nombres de tabla y columnas usadas. 
$tabla_objetivo   = 'actor'; // Tabla de Sakila donde están los actores.
$columna_pk       = 'actor_id'; // Clave primaria de la tabla actor.
$columna_editable = 'last_name'; // Columna que vamos a permitir editar.

// Intenta consultar la base de datos. 
try { // Abre bloque try para capturar errores.
    $conexion = obtener_conexion(); // Obtiene una conexión PDO.
    $consulta_sql = "SELECT actor_id, first_name, last_name, last_update FROM {$tabla_objetivo} ORDER BY actor_id ASC LIMIT 50"; // Define el SELECT básico.
    $sentencia = $conexion->query($consulta_sql); // Ejecuta la consulta directamente.
    $registros = $sentencia->fetchAll(); // Obtiene todas las filas como arreglo asociativo.
} catch (Throwable $error) { // Captura cualquier excepción durante la conexión o la consulta.
    http_response_code(500); // Establece código HTTP 500 por error interno.
    echo "<h1>Error al consultar la base de datos.</h1>"; // Muestra mensaje amigable.
    echo "<pre>" . htmlspecialchars($error->getMessage()) . "</pre>"; // Muestra detalle del error de forma segura.
    exit; // Termina la ejecución del script.
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Sakila App — Actores</title>
  <style>
    body { font-family: system-ui, Arial, sans-serif; margin: 2rem; }
    table { border-collapse: collapse; width: 100%; margin-top: 1rem; }
    th, td { border: 1px solid #ccc; padding: .5rem; text-align: left; }
    form { display: inline-flex; gap: .5rem; align-items: center; }
    input[type="text"] { width: 200px; }
    .nota { color:#555; }
  </style>
</head>
<body>
  <h1>Actores (tabla <code>actor</code>)</h1>
  <p class="nota">Edita el campo <code>last_name</code> y presiona “Actualizar”.</p>

  <?php if (!$registros): // Verifica si no hubo resultados. ?>
    <p>No hay registros para mostrar.</p>
  <?php else: // Si hay filas, arma la tabla. ?>
    <table>
      <thead>
        <tr>
          <th>actor_id</th>
          <th>first_name</th>
          <th>last_name</th>
          <th>last_update</th>
          <th>Editar last_name</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($registros as $fila): // Recorre cada fila del resultado. ?>
          <tr>
            <td><?= htmlspecialchars((string)$fila['actor_id']) ?></td> <!-- Muestra el id del actor. -->
            <td><?= htmlspecialchars($fila['first_name']) ?></td> <!-- Muestra el nombre del actor. -->
            <td><?= htmlspecialchars($fila['last_name']) ?></td> <!-- Muestra el apellido actual. -->
            <td><?= htmlspecialchars($fila['last_update']) ?></td> <!-- Muestra la fecha de última actualización. -->
            <td>
              <form action="actualizar.php" method="post"> <!-- Formulario que enviará el UPDATE. -->
                <input type="hidden" name="tabla_objetivo" value="<?= htmlspecialchars($tabla_objetivo) ?>"> <!-- Envía el nombre de la tabla. -->
                <input type="hidden" name="columna_pk" value="<?= htmlspecialchars($columna_pk) ?>"> <!-- Envía la columna PK. -->
                <input type="hidden" name="columna_editable" value="<?= htmlspecialchars($columna_editable) ?>"> <!-- Envía la columna a editar. -->
                <input type="hidden" name="valor_pk" value="<?= htmlspecialchars((string)$fila[$columna_pk]) ?>"> <!-- Envía el valor de la PK. -->
                <input type="text" name="nuevo_valor" placeholder="Nuevo apellido..." required> <!-- Campo con el nuevo apellido. -->
                <button type="submit">Actualizar</button> <!-- Botón de envío del formulario. -->
              </form>
            </td>
          </tr>
        <?php endforeach; // Fin del foreach. ?>
      </tbody>
    </table>
  <?php endif; // Fin de bloque de tabla. ?>
</body>
</html>
