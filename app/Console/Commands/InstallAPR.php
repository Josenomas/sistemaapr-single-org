<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;
use App\Models\Organizacion;
use App\Models\Usuario;

class InstallAPR extends Command
{
    protected $signature = 'apr:install
                            {--nombre= : Nombre de la organización APR}
                            {--rut= : RUT de la organización}
                            {--email= : Email del administrador}
                            {--password= : Contraseña del administrador}
                            {--telefono= : Teléfono de contacto}
                            {--direccion= : Dirección física}
                            {--ciudad= : Ciudad}
                            {--region= : Región}
                            {--superadmin-email= : Email del SuperAdmin (opcional)}
                            {--superadmin-password= : Contraseña del SuperAdmin (opcional)}';

    protected $description = 'Instalación inicial del Sistema APR para cliente único';

    public function handle()
    {
        $this->info('╔════════════════════════════════════════════════╗');
        $this->info('║   INSTALACIÓN SISTEMA APR - VERSIÓN CLIENTE    ║');
        $this->info('╚════════════════════════════════════════════════╝');
        $this->newLine();

        // Verificar si ya existe una organización
        if (Organizacion::count() > 0) {
            if (!$this->confirm('Ya existe una organización instalada. ¿Desea continuar y sobrescribirla?', false)) {
                $this->warn('Instalación cancelada.');
                return 1;
            }
        }

        // Recopilar datos
        $nombre = $this->option('nombre') ?? $this->ask('Nombre de la organización APR', 'APR Demo');
        $rut = $this->option('rut') ?? $this->ask('RUT de la organización', '12345678-9');
        $email = $this->option('email') ?? $this->ask('Email del administrador', 'admin@apr.cl');
        $password = $this->option('password') ?? $this->secret('Contraseña del administrador (mín. 6 caracteres)') ?? 'admin123';
        $telefono = $this->option('telefono') ?? $this->ask('Teléfono de contacto', '+56912345678');
        $direccion = $this->option('direccion') ?? $this->ask('Dirección física', 'Calle Principal 123');
        $ciudad = $this->option('ciudad') ?? $this->ask('Ciudad', 'Puerto Montt');
        $region = $this->option('region') ?? $this->ask('Región', 'Los Lagos');

        $this->newLine();
        $this->info('📋 Resumen de la instalación:');
        $this->table(
            ['Campo', 'Valor'],
            [
                ['Organización', $nombre],
                ['RUT', $rut],
                ['Email Admin', $email],
                ['Teléfono', $telefono],
                ['Dirección', $direccion],
                ['Ciudad', $ciudad],
                ['Región', $region],
            ]
        );

        if (!$this->confirm('¿Desea continuar con la instalación?', true)) {
            $this->warn('Instalación cancelada.');
            return 1;
        }

        try {
            // 1. Limpiar datos anteriores si existen
            $this->info('🗑️  Limpiando datos anteriores...');
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('users')->truncate();
            DB::table('usuarios')->truncate();
            DB::table('organizaciones')->truncate();
            DB::table('suscripciones')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            DB::beginTransaction();

            // 2. Crear suscripción por defecto
            $this->info('📋 Creando suscripción...');
            $suscripcion = DB::table('suscripciones')->insertGetId([
                'nombre' => 'plan_cliente',
                'nombre_mostrar' => 'Plan Cliente',
                'precio_mensual' => 0, // El SuperAdmin configurará el precio después
                'max_socios' => null, // Ilimitado
                'max_usuarios' => null, // Ilimitado
                'modulos_permitidos' => json_encode(['todos']),
                'features' => json_encode(['Todos los módulos', 'Soporte técnico', 'Actualizaciones incluidas']),
                'permite_dominio_personalizado' => true,
                'permite_modulo_noticias' => true,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 3. Crear organización
            $this->info('🏢 Creando organización...');
            $slug = \Illuminate\Support\Str::slug($nombre);
            $organizacion = Organizacion::create([
                'nombre_apr' => $nombre,
                'slug' => $slug,
                'rut' => $rut,
                'email' => $email,
                'telefono' => $telefono,
                'direccion' => $direccion,
                'ciudad' => $ciudad,
                'region' => $region,
                'id_suscripcion' => $suscripcion,
                'fecha_inicio_suscripcion' => now(),
                'fecha_fin_suscripcion' => now()->addYear(), // 1 año de suscripción inicial
                'estado_suscripcion' => 'activa',
                'activo' => 1,
            ]);

            // 4. Crear usuario administrador
            $this->info('👤 Creando usuario administrador...');
            $usuario = Usuario::create([
                'id_organizacion' => $organizacion->id,
                'nombre' => 'Admin',
                'apellido' => 'APR',
                'nombre_usuario' => 'admin',
                'email' => $email,
                'password' => Hash::make($password),
                'rol' => 'admin',
                'activo' => 1,
                'permisos' => json_encode([
                    'socios' => true,
                    'lecturas' => true,
                    'boletas' => true,
                    'pagos' => true,
                    'incidentes' => true,
                    'usuarios' => true,
                    'funcionarios' => true,
                    'sueldos' => true,
                    'cortes' => true,
                    'trabajos' => true,
                    'renovaciones' => true,
                    'vacaciones' => true,
                    'compras' => true,
                    'inventario' => true,
                    'movimientos_inventario' => true,
                    'tickets' => true,
                    'recordatorios' => true,
                    'notificaciones' => true,
                    'giros_bancarios' => true,
                    'directiva' => true,
                    'historial_consumo' => true,
                    'historial_pagos' => true,
                    'reportes' => true,
                    'eventos' => true,
                    'noticias' => true,
                ]),
            ]);

            // 5. Crear SuperAdmin si se proporcionaron credenciales
            $superadminEmail = $this->option('superadmin-email');
            $superadminPassword = $this->option('superadmin-password');

            if ($superadminEmail && $superadminPassword) {
                $this->info('👨‍💼 Creando SuperAdmin...');
                Usuario::create([
                    'nombre' => 'Super',
                    'apellido' => 'Admin',
                    'nombre_usuario' => 'superadmin',
                    'email' => $superadminEmail,
                    'password' => Hash::make($superadminPassword),
                    'rol' => 'superadmin',
                    'activo' => 1,
                ]);
            }

            DB::commit();

            $this->newLine();
            $this->info('✅ ¡Instalación completada exitosamente!');
            $this->newLine();
            $this->info('📌 Credenciales de acceso:');

            $credentials = [
                ['Tipo', 'URL', 'Email', 'Contraseña'],
                ['Admin', env('APP_URL', 'http://localhost'), $email, $password],
            ];

            if ($superadminEmail && $superadminPassword) {
                $credentials[] = ['SuperAdmin', env('APP_URL', 'http://localhost') . '/superadmin', $superadminEmail, $superadminPassword];
            }

            $this->table(
                ['Tipo', 'URL', 'Email', 'Contraseña'],
                array_slice($credentials, 1)
            );
            $this->newLine();
            $this->info('🔧 Próximos pasos:');
            $this->line('1. Personalizar landing page con datos de la organización');
            $this->line('2. Configurar Flow en .env (FLOW_API_KEY, FLOW_SECRET_KEY)');
            $this->line('3. Configurar email en .env (MAIL_*)');
            $this->line('4. Configurar DTE/SII si es necesario');
            $this->newLine();

            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Error durante la instalación: ' . $e->getMessage());
            return 1;
        }
    }
}
