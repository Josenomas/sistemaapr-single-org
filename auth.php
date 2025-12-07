<?php
/**
 * Sistema de Autenticación y Permisos
 * Sistema de Gestión Sanitaria Rural
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class Auth {

    /**
     * Verificar si el usuario está autenticado
     */
    public static function verificarSesion() {
        if (!isset($_SESSION['usuario_id'])) {
            header("Location: login.php");
            exit();
        }
    }

    /**
     * Obtener datos del usuario actual
     */
    public static function obtenerUsuario() {
        self::verificarSesion();

        return [
            'id' => $_SESSION['usuario_id'] ?? null,
            'username' => $_SESSION['username'] ?? null,
            'nombre' => $_SESSION['nombre'] ?? '',
            'apellido' => $_SESSION['apellido'] ?? '',
            'rol' => $_SESSION['rol'] ?? 'usuario'
        ];
    }

    /**
     * Verificar si el usuario tiene un permiso específico
     * @param string $permiso - Formato: 'modulo.accion' (ej: 'usuarios.crear')
     */
    public static function tienePermiso($permiso) {
        if (!isset($_SESSION['usuario_id'])) {
            return false;
        }

        $rol = $_SESSION['rol'] ?? 'usuario';

        // El administrador tiene todos los permisos
        if ($rol === 'Administrador' || $rol === 'admin') {
            return true;
        }

        // Aquí puedes implementar un sistema de permisos más complejo
        // Por ahora, algunos permisos básicos por rol
        $permisos_por_rol = [
            'Administrador' => ['*'], // Todos los permisos
            'Operador' => [
                'pacientes.*',
                'consultas.*',
                'medicamentos.ver',
                'medicamentos.crear',
                'inventario.ver'
            ],
            'Enfermero' => [
                'pacientes.ver',
                'pacientes.crear',
                'consultas.ver',
                'consultas.crear',
                'medicamentos.ver'
            ],
            'Auxiliar' => [
                'pacientes.ver',
                'consultas.ver',
                'medicamentos.ver'
            ],
            'usuario' => [
                'pacientes.ver',
                'consultas.ver'
            ]
        ];

        // Obtener permisos del rol
        $permisos = $permisos_por_rol[$rol] ?? [];

        // Verificar si tiene permiso
        if (in_array('*', $permisos)) {
            return true;
        }

        if (in_array($permiso, $permisos)) {
            return true;
        }

        // Verificar permisos con wildcard (ej: 'pacientes.*')
        list($modulo, $accion) = explode('.', $permiso);
        if (in_array($modulo . '.*', $permisos)) {
            return true;
        }

        return false;
    }

    /**
     * Verificar permiso y redirigir si no lo tiene
     */
    public static function requierePermiso($permiso) {
        if (!self::tienePermiso($permiso)) {
            header("Location: index.php?error=sin_permiso");
            exit();
        }
    }

    /**
     * Cerrar sesión
     */
    public static function cerrarSesion() {
        session_start();
        session_unset();
        session_destroy();
        header("Location: login.php");
        exit();
    }

    /**
     * Obtener el rol del usuario actual
     */
    public static function obtenerRol() {
        return $_SESSION['rol'] ?? 'usuario';
    }

    /**
     * Verificar si el usuario es administrador
     */
    public static function esAdministrador() {
        $rol = self::obtenerRol();
        return $rol === 'Administrador' || $rol === 'admin';
    }
}

// Verificar sesión automáticamente en todas las páginas que incluyan este archivo
// excepto en login.php
$archivo_actual = basename($_SERVER['PHP_SELF']);
if ($archivo_actual !== 'login.php') {
    Auth::verificarSesion();
}
?>
