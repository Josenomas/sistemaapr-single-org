<?php
/**
 * Cerrar sesión del usuario
 * Sistema de Gestión Sanitaria Rural
 */

session_start();

// Destruir todas las variables de sesión
$_SESSION = array();

// Si se desea destruir la sesión completamente, borrar también la cookie de sesión
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-3600, '/');
}

// Finalmente, destruir la sesión
session_destroy();

// Redirigir al login
header("Location: login.php");
exit();
?>
