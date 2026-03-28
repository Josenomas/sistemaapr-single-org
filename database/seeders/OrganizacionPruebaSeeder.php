<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Organizacion;
use App\Models\Suscripcion;
use App\Models\Usuario;
use App\Models\Socio;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class OrganizacionPruebaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Obtener el plan Profesional
        $planProfesional = Suscripcion::where('nombre', 'profesional')->first();

        if (!$planProfesional) {
            $this->command->error('No se encontró el plan Profesional. Ejecuta primero SuscripcionesSeeder.');
            return;
        }

        // Crear o buscar organización de prueba
        $organizacion = Organizacion::firstOrCreate(
            ['slug' => 'apr-prueba'],
            [
                'nombre_apr' => 'APR Prueba Desarrollo',
                'rut' => '12345678-9',
                'direccion' => 'Calle Principal 123',
                'telefono' => '+56912345678',
                'email_contacto' => 'contacto@aprprueba.cl',
                'dominio_personalizado' => null,
                'id_suscripcion' => $planProfesional->id,
                'fecha_inicio_suscripcion' => Carbon::now(),
                'fecha_fin_suscripcion' => Carbon::now()->addMonths(1),
                'estado_suscripcion' => 'activa',
                'dias_prueba_restantes' => 0,
                'metodo_pago' => 'transferencia',
                'proximo_pago' => Carbon::now()->addMonths(1),
                'activo' => true,
            ]
        );

        if ($organizacion->wasRecentlyCreated) {
            $this->command->info("✓ Organización creada: {$organizacion->nombre_apr}");
        } else {
            $this->command->info("✓ Organización ya existe: {$organizacion->nombre_apr}");
        }

        // Asignar el primer usuario existente a esta organización, o crear uno nuevo
        $primerUsuario = Usuario::first();

        if (!$primerUsuario) {
            // Crear usuario de prueba
            $primerUsuario = Usuario::create([
                'nombre_usuario' => 'admin',
                'nombre' => 'Admin',
                'apellido' => 'APR',
                'email' => 'admin@aprprueba.cl',
                'password' => Hash::make('admin123'),
                'id_organizacion' => $organizacion->id,
                'rol' => 'admin',
                'activo' => true
            ]);
            $this->command->info("✓ Usuario de prueba creado: {$primerUsuario->nombre_usuario} (password: admin123)");
        } else {
            $primerUsuario->update([
                'id_organizacion' => $organizacion->id,
                'rol' => 'admin'
            ]);
            $this->command->info("✓ Usuario '{$primerUsuario->nombre} {$primerUsuario->apellido}' asignado como admin");
        }

        // Asignar todos los datos existentes a esta organización
        $tablasAsignar = [
            'socios', 'boletas', 'pagos', 'lecturas', 'historial_consumo',
            'incidentes', 'cortes_suministro', 'renovaciones_medidores',
            'funcionarios', 'sueldos', 'vacaciones', 'directiva',
            'inventario', 'movimientos_inventario', 'movimientos_inventario_detalle',
            'compras', 'giros_bancarios', 'configuraciones_tarifas', 'tarifas',
            'notificaciones', 'recordatorios', 'eventos', 'tickets', 'ticket_respuestas',
            'activos_fijos', 'rendiciones_mensuales',
            'trabajos_realizados', 'transacciones_flow'
        ];

        $totalAsignados = 0;
        foreach ($tablasAsignar as $tabla) {
            $count = \DB::table($tabla)
                ->whereNull('id_organizacion')
                ->update(['id_organizacion' => $organizacion->id]);
            $totalAsignados += $count;
        }

        $this->command->info("✓ {$totalAsignados} registros asignados a la organización en todas las tablas");

        // Crear o buscar organización Enterprise de prueba
        $planEnterprise = Suscripcion::where('nombre', 'enterprise')->first();

        if ($planEnterprise) {
            $orgEnterprise = Organizacion::firstOrCreate(
                ['slug' => 'apr-enterprise'],
                [
                    'nombre_apr' => 'APR Enterprise Demo',
                    'rut' => '98765432-1',
                    'direccion' => 'Avenida Principal 456',
                    'telefono' => '+56987654321',
                    'email_contacto' => 'contacto@aprenterprise.cl',
                    'dominio_personalizado' => 'www.aprenterprise.cl',
                    'id_suscripcion' => $planEnterprise->id,
                    'fecha_inicio_suscripcion' => Carbon::now(),
                    'fecha_fin_suscripcion' => Carbon::now()->addYear(),
                    'estado_suscripcion' => 'activa',
                    'dias_prueba_restantes' => 0,
                    'metodo_pago' => 'transbank',
                    'proximo_pago' => Carbon::now()->addMonths(1),
                    'logo' => null,
                    'color_primario' => '#3b82f6',
                    'color_secundario' => '#10b981',
                    'activo' => true,
                ]
            );

            if ($orgEnterprise->wasRecentlyCreated) {
                $this->command->info("✓ Organización Enterprise creada: {$orgEnterprise->nombre_apr}");
            } else {
                $this->command->info("✓ Organización Enterprise ya existe: {$orgEnterprise->nombre_apr}");
            }
        }

        $this->command->info("\n===========================================");
        $this->command->info("DATOS DE PRUEBA CREADOS EXITOSAMENTE");
        $this->command->info("===========================================");
        $this->command->info("Organización: APR Prueba Desarrollo");
        $this->command->info("Plan: Profesional");
        $this->command->info("Usuario: {$primerUsuario->nombre_usuario} (password: admin123)");
        $this->command->info("Registros asignados: {$totalAsignados}");
        $this->command->info("===========================================\n");
    }
}
