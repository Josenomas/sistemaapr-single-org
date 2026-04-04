<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Organizacion;
use App\Models\Suscripcion;
use App\Models\Usuario;
use App\Models\Socio;
use App\Models\ConfiguracionTarifa;
use App\Models\Lectura;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class OrganizacionBasicaConSociosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::beginTransaction();

        try {
            // 1. Obtener o crear el plan Básico
            $planBasico = Suscripcion::firstOrCreate(
                ['nombre' => 'basico'],
                [
                    'nombre_mostrar' => 'Plan Básico',
                    'precio_mensual' => 9990,
                    'max_socios' => 100,
                    'max_usuarios' => 3,
                    'modulos_permitidos' => json_encode(['socios', 'lecturas', 'boletas', 'pagos', 'dashboard']),
                    'features' => json_encode([
                        'Gestión de hasta 100 socios',
                        'Módulo de lecturas y consumo',
                        'Generación de boletas',
                        'Registro de pagos',
                        'Dashboard básico'
                    ]),
                    'permite_dominio_personalizado' => false,
                    'permite_modulo_noticias' => false,
                    'activo' => true,
                ]
            );

            $this->command->info("✓ Plan Básico: {$planBasico->nombre_mostrar}");

            // 2. Crear organización de prueba con plan básico
            $organizacion = Organizacion::create([
                'nombre_apr' => 'APR Santa Rosa de Prueba',
                'rut' => '76543210-5',
                'slug' => 'apr-santa-rosa-prueba',
                'direccion' => 'Camino Rural Km 15, Santa Rosa',
                'telefono' => '+56945123456',
                'email_contacto' => 'santarosa@aprprueba.cl',
                'dominio_personalizado' => null,
                'id_suscripcion' => $planBasico->id,
                'fecha_inicio_suscripcion' => Carbon::now()->subDays(29), // Hace 29 días
                'fecha_fin_suscripcion' => Carbon::now()->addDay(), // Vence mañana
                'estado_suscripcion' => 'activa',
                'dias_prueba_restantes' => 0,
                'metodo_pago' => 'transferencia',
                'proximo_pago' => Carbon::now()->addDay(), // Próximo pago mañana
                'activo' => true,
            ]);

            $this->command->info("✓ Organización creada: {$organizacion->nombre_apr}");
            $this->command->warn("  ⚠️  Suscripción vence: " . $organizacion->fecha_fin_suscripcion->format('d/m/Y H:i'));

            // 3. Crear usuario admin
            $usuario = Usuario::create([
                'id_organizacion' => $organizacion->id,
                'nombre_usuario' => 'admin.santarosa',
                'nombre' => 'Juan',
                'apellido' => 'Administrador',
                'email' => 'admin@santarosa.cl',
                'password' => Hash::make('admin123'),
                'rol' => 'admin',
                'activo' => true
            ]);

            $this->command->info("✓ Usuario creado: {$usuario->nombre_usuario} / password: admin123");

            // 4. Crear configuración de tarifas
            ConfiguracionTarifa::create([
                'id_organizacion' => $organizacion->id,
                'nombre' => 'Tramo 1 (0-10 m³)',
                'tipo_cliente' => 'residencial',
                'nombre_tarifa' => 'Tarifa Residencial 2026',
                'consumo_desde' => 0,
                'consumo_hasta' => 10,
                'monto' => 500,
                'cargo_fijo' => 3500,
                'iva' => 19,
                'vigente_desde' => Carbon::now()->startOfYear(),
                'vigente_hasta' => null,
                'orden' => 1,
                'activo' => true
            ]);

            ConfiguracionTarifa::create([
                'id_organizacion' => $organizacion->id,
                'nombre' => 'Tramo 2 (11-20 m³)',
                'tipo_cliente' => 'residencial',
                'nombre_tarifa' => 'Tarifa Residencial 2026',
                'consumo_desde' => 11,
                'consumo_hasta' => 20,
                'monto' => 650,
                'cargo_fijo' => 0,
                'iva' => 19,
                'vigente_desde' => Carbon::now()->startOfYear(),
                'vigente_hasta' => null,
                'orden' => 2,
                'activo' => true
            ]);

            ConfiguracionTarifa::create([
                'id_organizacion' => $organizacion->id,
                'nombre' => 'Tramo 3 (21+ m³)',
                'tipo_cliente' => 'residencial',
                'nombre_tarifa' => 'Tarifa Residencial 2026',
                'consumo_desde' => 21,
                'consumo_hasta' => null,
                'monto' => 800,
                'cargo_fijo' => 0,
                'iva' => 19,
                'vigente_desde' => Carbon::now()->startOfYear(),
                'vigente_hasta' => null,
                'orden' => 3,
                'activo' => true
            ]);

            $this->command->info("✓ Tarifas creadas (3 tramos)");

            // 5. Crear 100 socios ficticios
            $nombres = ['Juan', 'María', 'Pedro', 'Ana', 'Carlos', 'Rosa', 'Luis', 'Carmen', 'José', 'Patricia',
                       'Miguel', 'Laura', 'Jorge', 'Isabel', 'Francisco', 'Elena', 'Antonio', 'Sofía', 'Manuel', 'Lucia'];
            $apellidos = ['González', 'Rodríguez', 'Pérez', 'Sánchez', 'Ramírez', 'Torres', 'Flores', 'Rivera',
                         'Gómez', 'Díaz', 'Cruz', 'Morales', 'Muñoz', 'Rojas', 'Jiménez', 'Hernández', 'Medina',
                         'Castro', 'Vargas', 'Ortiz'];
            $sectores = ['Centro', 'Norte', 'Sur', 'Este', 'Oeste', 'Alto', 'Bajo'];

            $this->command->info("Creando 100 socios ficticios...");
            $bar = $this->command->getOutput()->createProgressBar(100);
            $bar->start();

            for ($i = 1; $i <= 100; $i++) {
                $nombre = $nombres[array_rand($nombres)];
                $apellidoPaterno = $apellidos[array_rand($apellidos)];
                $apellidoMaterno = $apellidos[array_rand($apellidos)];
                $sector = $sectores[array_rand($sectores)];

                // Generar RUT ficticio único
                $rutNumero = 10000000 + $i * 100;
                $rut = $rutNumero . '-' . rand(0, 9);

                $socio = Socio::create([
                    'id_organizacion' => $organizacion->id,
                    'numero_socio' => 'SOC-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                    'rut' => $rut,
                    'nombre' => $nombre,
                    'apellido_paterno' => $apellidoPaterno,
                    'apellido_materno' => $apellidoMaterno,
                    'direccion' => "Calle {$i}, Sector {$sector}",
                    'sector' => $sector,
                    'telefono' => '+569' . rand(10000000, 99999999),
                    'email' => null,
                    'tipo_cliente' => 'residencial',
                    'exento_iva' => false,
                    'subsidio_porcentaje' => ($i <= 20) ? 30 : 0, // 20% de socios con subsidio
                    'descuento_monto' => 0,
                    'numero_medidor' => 'MED-' . str_pad($i, 5, '0', STR_PAD_LEFT),
                    'estado' => 'activo',
                    'fecha_ingreso' => Carbon::now()->subMonths(rand(1, 24)),
                    'observaciones' => null,
                    'activo' => true,
                ]);

                // Crear lectura del mes actual
                $consumo = rand(5, 35); // Entre 5 y 35 m³
                Lectura::create([
                    'id_organizacion' => $organizacion->id,
                    'id_socio' => $socio->id,
                    'mes' => Carbon::now()->format('Y-m'),
                    'lectura_anterior' => rand(100, 500),
                    'lectura_actual' => rand(100, 500) + $consumo,
                    'consumo_m3' => $consumo,
                    'fecha_lectura' => Carbon::now()->subDays(rand(1, 5)),
                    'observaciones' => null,
                    'id_usuario_registro' => $usuario->id,
                ]);

                $bar->advance();
            }

            $bar->finish();
            $this->command->newLine(2);
            $this->command->info("✓ 100 socios creados con lecturas del mes actual");

            DB::commit();

            // Resumen final
            $this->command->newLine();
            $this->command->info("╔═══════════════════════════════════════════════════════════════╗");
            $this->command->info("║         ORGANIZACIÓN DE PRUEBA CREADA EXITOSAMENTE            ║");
            $this->command->info("╠═══════════════════════════════════════════════════════════════╣");
            $this->command->info("║ Organización: APR Santa Rosa de Prueba                        ║");
            $this->command->info("║ Plan: Básico (100 socios max)                                 ║");
            $this->command->info("║ Precio: \$9.990/mes                                            ║");
            $this->command->warn("║ ⚠️  Vence: " . $organizacion->fecha_fin_suscripcion->format('d/m/Y H:i') . "                             ║");
            $this->command->warn("║ ⚠️  Próximo pago: " . $organizacion->proximo_pago->format('d/m/Y') . "                              ║");
            $this->command->info("╠═══════════════════════════════════════════════════════════════╣");
            $this->command->info("║ Usuario: admin.santarosa                                      ║");
            $this->command->info("║ Password: admin123                                            ║");
            $this->command->info("║ Email: admin@santarosa.cl                                     ║");
            $this->command->info("╠═══════════════════════════════════════════════════════════════╣");
            $this->command->info("║ Socios creados: 100/100                                       ║");
            $this->command->info("║ - 80 socios normales                                          ║");
            $this->command->info("║ - 20 socios con subsidio 30%                                  ║");
            $this->command->info("║ Lecturas: 100 (mes actual)                                    ║");
            $this->command->info("║ Tarifas: 3 tramos configurados                                ║");
            $this->command->info("╚═══════════════════════════════════════════════════════════════╝");
            $this->command->newLine();

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error("Error: " . $e->getMessage());
            throw $e;
        }
    }
}
