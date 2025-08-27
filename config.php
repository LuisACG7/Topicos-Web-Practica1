<?php
$servidor_bd = 'localhost'; // IP de loopback para el motor MySQL/MariaDB local.

// Puerto del servicio de base de datos. 
$puerto_bd = 3306; // Puerto estándar de MySQL/MariaDB.

$nombre_bd = 'sakila'; 

// Usuario con permisos sobre la base. 
$usuario_bd = '20031609'; 
 
$contrasena_bd = '20031609'; 

// Opciones de PDO para manejo de errores y formato de resultados. 
$opciones_pdo = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Lanza excepciones cuando ocurre un error SQL.
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Devuelve filas como arreglos asociativos.
    PDO::ATTR_PERSISTENT => false, // Desactiva conexión persistente para simplicidad.
];

// Función que devuelve una conexión lista para usar en otras páginas. 
function obtener_conexion(): PDO { 
    global $servidor_bd, $puerto_bd, $nombre_bd, $usuario_bd, $contrasena_bd, $opciones_pdo; // Importa variables globales.
    $cadena_dsn = "mysql:host={$servidor_bd};port={$puerto_bd};dbname={$nombre_bd};charset=utf8mb4"; // Construye el DSN de conexión.
    return new PDO($cadena_dsn, $usuario_bd, $contrasena_bd, $opciones_pdo); // Crea y retorna la instancia PDO.
}
