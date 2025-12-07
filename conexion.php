<?php
// Configuración de la base de datos
$host = 'localhost';
$usuario_bd = 'root';
$password_bd = '';
$nombre_bd = 'sistema_apr';

// Crear conexión
$conexion = new mysqli($host, $usuario_bd, $password_bd, $nombre_bd);

// Verificar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Establecer charset UTF-8
$conexion->set_charset("utf8mb4");
?>
