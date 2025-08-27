<?php
// config.php.
// Archivo de configuración y conexión a MariaDB/MySQL usando PDO.

// Servidor de base de datos a conectar (localhost en WSL). 
$servidor_bd = '127.0.0.1'; // IP de loopback para el motor MySQL/MariaDB local.

// Puerto del servicio de base de datos. 
$puerto_bd = 3306; // Puerto estándar de MySQL/MariaDB.

// Nombre de la base de datos que vamos a usar. 
$nombre_bd = 'sakila'; // Base de datos importada con tus scripts sakila-schema y sakila-data.

// Usuario con permisos sobre esa base. 
$usuario_bd = 'luis'; // Usuario que creaste para prácticas.

// Contraseña del usuario anterior. 
$contrasena_bd = '1234'; // Contraseña correspondiente al usuario 'alumno'.

// Opciones de PDO para manejo de errores y formato de resultados. 
$opciones_pdo = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Lanza excepciones cuando ocurre un error SQL.
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Devuelve filas como arreglos asociativos.
    PDO::ATTR_PERSISTENT => false, // Desactiva conexión persistente para simplicidad.
];

// Función que devuelve una conexión lista para usar en otras páginas. 
function obtener_conexion(): PDO { // Declara tipo de retorno PDO.
    global $servidor_bd, $puerto_bd, $nombre_bd, $usuario_bd, $contrasena_bd, $opciones_pdo; // Importa variables globales.
    $cadena_dsn = "mysql:host={$servidor_bd};port={$puerto_bd};dbname={$nombre_bd};charset=utf8mb4"; // Construye el DSN de conexión.
    return new PDO($cadena_dsn, $usuario_bd, $contrasena_bd, $opciones_pdo); // Crea y retorna la instancia PDO.
}
