<?php

// Importa la configuración y la función de conexión PDO.
require_once __DIR__ . '/config.php'; // Incluye el archivo config.php para obtener la conexión a la base de datos.

// Define nombres de tabla y columnas a usar.
$tabla_objetivo = 'actor'; 
$columna_nombre = 'first_name'; 
$columna_apellido = 'last_name'; 

// Intenta realizar la consulta SELECT.
try { // Abre un bloque try/catch para manejar excepciones.
    $conexion = obtener_conexion(); 
    $consulta_sql = "SELECT {$columna_nombre}, {$columna_apellido} FROM {$tabla_objetivo} ORDER BY {$columna_nombre} ASC, {$columna_apellido} ASC LIMIT 25"; 
    $sentencia = $conexion->query($consulta_sql); 
    $registros = $sentencia->fetchAll(); 
} catch (Throwable $error) { // Captura cualquier excepción de conexión o consulta.
    http_response_code(500); // Establece el código HTTP 500 para indicar error del servidor.
    echo "<h1>Error al consultar la base de datos.</h1>"; 
    echo "<pre>" . htmlspecialchars($error->getMessage()) . "</pre>"; 
    exit; 
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8"> 
  <title>Sakila App — Actores</title> 
  <style>
    /* Estilos básicos para presentar la tabla. */
    body { font-family: system-ui, Arial, sans-serif; margin: 2rem; } 
    table { border-collapse: collapse; width: 100%; margin-top: 1rem; } 
    th, td { border: 1px solid #ccc; padding: .5rem; text-align: left; } 
  </style>
</head>
<body>
  <h1>Actores</h1> 
  <?php if (!$registros): // Verifica si no hay resultados. ?>
    <p>No hay registros para mostrar.</p> 
  <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>first_name</th> 
          <th>last_name</th> 
        </tr>
      </thead>
      <tbody>
        <?php foreach ($registros as $fila): ?>
          <tr>
            <td><?= htmlspecialchars($fila['first_name']) ?></td> 
            <td><?= htmlspecialchars($fila['last_name']) ?></td> 
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</body>
</html>
