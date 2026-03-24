<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SuscripcionesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $planes = [
            [
                'nombre' => 'basico',
                'nombre_mostrar' => 'Básico',
                'precio_mensual' => 20000.00,
                'max_socios' => 100,
                'max_usuarios' => 1,
                'modulos_permitidos' => json_encode([
                    'socios',
                    'lecturas',
                    'boletas',
                    'pagos'
                ]),
                'features' => json_encode([
                    'Hasta 100 socios',
                    'Módulos básicos',
                    '1 usuario administrador',
                    'Soporte por email',
                    'Actualizaciones incluidas',
                    'Subdominio personalizado'
                ]),
                'permite_dominio_personalizado' => false,
                'permite_modulo_noticias' => false,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'profesional',
                'nombre_mostrar' => 'Profesional',
                'precio_mensual' => 30000.00,
                'max_socios' => 500,
                'max_usuarios' => 5,
                'modulos_permitidos' => json_encode([
                    'socios',
                    'lecturas',
                    'boletas',
                    'pagos',
                    'trabajos',
                    'funcionarios',
                    'usuarios',
                    'inventario',
                    'movimientos-inventario',
                    'cortes',
                    'incidentes',
                    'notificaciones',
                    'reportes',
                    'directiva',
                    'activos-fijos',
                    'compras',
                    'sueldos',
                    'vacaciones',
                    'recordatorios',
                    'eventos',
                    'tickets'
                ]),
                'features' => json_encode([
                    'Hasta 500 socios',
                    'Todos los módulos administrativos',
                    '5 usuarios',
                    'Soporte prioritario por email',
                    'Notificaciones por email y WhatsApp',
                    'Reportes avanzados',
                    'Subdominio personalizado'
                ]),
                'permite_dominio_personalizado' => false,
                'permite_modulo_noticias' => false,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'enterprise',
                'nombre_mostrar' => 'Enterprise',
                'precio_mensual' => 50000.00,
                'max_socios' => null, // ilimitado
                'max_usuarios' => null, // ilimitado
                'modulos_permitidos' => json_encode([
                    'socios',
                    'lecturas',
                    'boletas',
                    'pagos',
                    'trabajos',
                    'funcionarios',
                    'usuarios',
                    'inventario',
                    'movimientos-inventario',
                    'cortes',
                    'incidentes',
                    'notificaciones',
                    'reportes',
                    'directiva',
                    'activos-fijos',
                    'compras',
                    'sueldos',
                    'vacaciones',
                    'recordatorios',
                    'eventos',
                    'tickets',
                    'noticias', // exclusivo Enterprise
                    'renovaciones'
                ]),
                'features' => json_encode([
                    'Socios ilimitados',
                    'Todos los módulos',
                    'Usuarios ilimitados',
                    'Soporte 24/7',
                    'Personalización de marca',
                    'Integración API',
                    'Dominio personalizado',
                    'Módulo de noticias públicas'
                ]),
                'permite_dominio_personalizado' => true,
                'permite_modulo_noticias' => true,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('suscripciones')->insert($planes);
    }
}
