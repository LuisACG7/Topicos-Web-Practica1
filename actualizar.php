<?php
// actualizar.php.
// Recibe datos por POST, valida, y hace UPDATE del apellido en la tabla actor.

require_once __DIR__ . '/config.php'; // Incluye la conexión PDO.

// Recupera parámetros recibidos desde el formulario. 
$tabla_objetivo   = $_POST['tabla_objetivo']   ?? ''; // Nombre de la tabla a actualizar.
$columna_pk       = $_POST['columna_pk']       ?? ''; // Nombre de la columna PK.
$columna_editable = $_POST['columna_editable'] ?? ''; // Nombre de la columna a editar.
$valor_pk         = $_POST['valor_pk']         ?? ''; // Valor concreto de la PK del registro.
$nuevo_valor      = $_POST['nuevo_valor']      ?? ''; // Nuevo valor para la columna editable.

// Valida que todos los parámetros requeridos existan. 
if ($tabla_objetivo === '' || $columna_pk === '' || $columna_editable === '' || $valor_pk === '' || $nuevo_valor === '') {
    http_response_code(400); // Marca error de solicitud.
    echo "Parámetros incompletos."; // Mensaje de error.
    exit; // Termina ejecución.
}

// Lista blanca simple para nombres de tabla y columnas (evita inyección en identificadores). 
$patron_identificador = '/^[a-zA-Z0-9_]+$/'; // Acepta letras, números y guion bajo.
if (
    !preg_match($patron_identificador, $tabla_objetivo) ||
    !preg_match($patron_identificador, $columna_pk) ||
    !preg_match($patron_identificador, $columna_editable)
) {
    http_response_code(400); // Petición inválida.
    echo "Nombres de tabla o columna inválidos."; // Mensaje de error.
    exit; // Fin.
}

try { // Intenta ejecutar el UPDATE.
    $conexion = obtener_conexion(); // Obtiene conexión PDO.

    // Construye la sentencia de UPDATE con placeholders para los valores. 
    $sql_update = "UPDATE {$tabla_objetivo} SET {$columna_editable} = :nuevo_valor WHERE {$columna_pk} = :valor_pk LIMIT 1"; // Sentencia SQL parametrizada.

    $stmt = $conexion->prepare($sql_update); // Prepara la sentencia en el servidor.
    $stmt->bindValue(':nuevo_valor', $nuevo_valor); // Asigna el nuevo valor al placeholder.
    $stmt->bindValue(':valor_pk', $valor_pk); // Asigna la PK al placeholder correspondiente.
    $stmt->execute(); // Ejecuta el UPDATE en la base de datos.

    header('Location: index.php'); // Redirige de nuevo a la lista para ver cambios.
    exit; // Termina el script.
} catch (Throwable $error) { // Captura cualquier error durante el proceso.
    http_response_code(500); // Marca error interno del servidor.
    echo "<h1>Error actualizando el registro.</h1>"; // Mensaje amigable.
    echo "<pre>" . htmlspecialchars($error->getMessage()) . "</pre>"; // Detalle del error de forma segura.
    exit; // Fin.
}
