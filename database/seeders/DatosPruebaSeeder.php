<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Suscripcion;
use App\Models\Organizacion;
use App\Models\Usuario;
use App\Models\Socio;
use App\Models\Lectura;
use App\Models\Boleta;
use App\Models\Pago;
use App\Models\ConfiguracionTarifa;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class DatosPruebaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. SUPER ADMIN (sin organización)
        $superAdmin = Usuario::create([
            'id_organizacion' => null,
            'nombre_usuario' => 'superadmin',
            'password' => Hash::make('admin123'),
            'nombre' => 'Super',
            'apellido' => 'Administrador',
            'email' => 'superadmin@sistemaapr.cl',
            'telefono' => '+56912345678',
            'rol' => 'superadmin',
            'activo' => true,
        ]);

        echo "✓ Super Admin creado: superadmin@sistemaapr.cl / admin123\n";

        // 2. ORGANIZACIÓN 1 - APR Las Rosas (Plan Profesional)
        $suscripcionProfesional = Suscripcion::where('nombre', 'profesional')->first();

        $org1 = Organizacion::create([
            'nombre_apr' => 'APR Las Rosas',
            'rut' => '76123456-7',
            'direccion' => 'Calle Principal 123, Las Rosas',
            'telefono' => '+56945678901',
            'email_contacto' => 'contacto@aprlasrosas.cl',
            'slug' => 'lasrosas',
            'dominio_personalizado' => null,
            'estado_dominio_personalizado' => 'sin_configurar',
            'id_suscripcion' => $suscripcionProfesional->id,
            'fecha_inicio_suscripcion' => Carbon::now(),
            'fecha_fin_suscripcion' => Carbon::now()->addYear(),
            'estado_suscripcion' => 'activa',
            'proximo_pago' => Carbon::now()->addMonth(),
            'activo' => true,
            'color_primario' => '#3498db',
            'color_secundario' => '#2ecc71',
        ]);

        echo "✓ Organización 1 creada: APR Las Rosas (Profesional)\n";

        // Usuarios de APR Las Rosas
        $adminOrg1 = Usuario::create([
            'id_organizacion' => $org1->id,
            'nombre_usuario' => 'admin.lasrosas',
            'password' => Hash::make('admin123'),
            'nombre' => 'Juan',
            'apellido' => 'Pérez',
            'email' => 'admin@aprlasrosas.cl',
            'telefono' => '+56923456789',
            'rol' => 'admin',
            'activo' => true,
        ]);

        $tesoreroOrg1 = Usuario::create([
            'id_organizacion' => $org1->id,
            'nombre_usuario' => 'tesorero.lasrosas',
            'password' => Hash::make('tesorero123'),
            'nombre' => 'María',
            'apellido' => 'González',
            'email' => 'tesoreria@aprlasrosas.cl',
            'telefono' => '+56934567890',
            'rol' => 'tesorero',
            'activo' => true,
        ]);

        echo "  → Admin: admin@aprlasrosas.cl / admin123\n";
        echo "  → Tesorero: tesoreria@aprlasrosas.cl / tesorero123\n";

        // Socios de APR Las Rosas
        $sociosOrg1 = [];
        for ($i = 1; $i <= 10; $i++) {
            $sociosOrg1[] = Socio::create([
                'id_organizacion' => $org1->id,
                'numero_socio' => 'LR-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'rut' => '1' . rand(1000000, 9999999) . '-' . rand(0, 9),
                'nombre' => 'Socio ' . $i,
                'apellido_paterno' => 'Apellido' . $i,
                'apellido_materno' => 'Materno' . $i,
                'direccion' => 'Calle ' . $i . ' #' . rand(100, 999),
                'sector' => $i <= 5 ? 'Centro' : 'Norte',
                'telefono' => '+569' . rand(10000000, 99999999),
                'email' => 'socio' . $i . '@email.com',
                'tipo_cliente' => 'residencial',
                'numero_medidor' => 'MED-' . rand(1000, 9999),
                'estado' => 'activo',
                'fecha_ingreso' => Carbon::now()->subMonths(rand(1, 24)),
                'activo' => true,
            ]);
        }

        echo "  → 10 socios creados\n";

        // 3. ORGANIZACIÓN 2 - APR El Valle (Plan Enterprise - Con dominio en verificación)
        $suscripcionEnterprise = Suscripcion::where('nombre', 'enterprise')->first();

        $org2 = Organizacion::create([
            'nombre_apr' => 'APR El Valle',
            'rut' => '76234567-8',
            'direccion' => 'Av. Central 456, El Valle',
            'telefono' => '+56956789012',
            'email_contacto' => 'info@aprelvalle.cl',
            'slug' => 'elvalle',
            'dominio_personalizado' => 'www.aprelvalle.test',
            'estado_dominio_personalizado' => 'verificado_dns',
            'fecha_solicitud_dominio' => Carbon::now()->subDays(2),
            'fecha_verificacion_dns' => Carbon::now()->subHours(6),
            'id_suscripcion' => $suscripcionEnterprise->id,
            'fecha_inicio_suscripcion' => Carbon::now()->subMonths(6),
            'fecha_fin_suscripcion' => Carbon::now()->addMonths(6),
            'estado_suscripcion' => 'activa',
            'proximo_pago' => Carbon::now()->addMonth(),
            'activo' => true,
            'color_primario' => '#9b59b6',
            'color_secundario' => '#e74c3c',
        ]);

        echo "✓ Organización 2 creada: APR El Valle (Enterprise) - Dominio verificado DNS\n";

        // Usuarios de APR El Valle
        Usuario::create([
            'id_organizacion' => $org2->id,
            'nombre_usuario' => 'admin.elvalle',
            'password' => Hash::make('admin123'),
            'nombre' => 'Carlos',
            'apellido' => 'Rodríguez',
            'email' => 'admin@aprelvalle.cl',
            'telefono' => '+56945678902',
            'rol' => 'admin',
            'activo' => true,
        ]);

        Usuario::create([
            'id_organizacion' => $org2->id,
            'nombre_usuario' => 'operador.elvalle',
            'password' => Hash::make('operador123'),
            'nombre' => 'Ana',
            'apellido' => 'Martínez',
            'email' => 'operador@aprelvalle.cl',
            'telefono' => '+56956789013',
            'rol' => 'operador',
            'activo' => true,
        ]);

        echo "  → Admin: admin@aprelvalle.cl / admin123\n";
        echo "  → Operador: operador@aprelvalle.cl / operador123\n";

        // Socios de APR El Valle
        for ($i = 1; $i <= 15; $i++) {
            Socio::create([
                'id_organizacion' => $org2->id,
                'numero_socio' => 'EV-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'rut' => '1' . rand(1000000, 9999999) . '-' . rand(0, 9),
                'nombre' => 'Usuario ' . $i,
                'apellido_paterno' => 'Valle' . $i,
                'apellido_materno' => 'Test' . $i,
                'direccion' => 'Pasaje ' . $i . ' #' . rand(10, 99),
                'sector' => ['Centro', 'Norte', 'Sur', 'Este'][rand(0, 3)],
                'telefono' => '+569' . rand(10000000, 99999999),
                'email' => 'usuario' . $i . '@elvalle.com',
                'tipo_cliente' => $i <= 10 ? 'residencial' : 'comercial',
                'numero_medidor' => 'MEDV-' . rand(1000, 9999),
                'estado' => 'activo',
                'fecha_ingreso' => Carbon::now()->subMonths(rand(1, 36)),
                'activo' => true,
            ]);
        }

        echo "  → 15 socios creados\n";

        // 4. ORGANIZACIÓN 3 - APR Agua Clara (Plan Básico - En período de prueba)
        $suscripcionBasico = Suscripcion::where('nombre', 'basico')->first();

        $org3 = Organizacion::create([
            'nombre_apr' => 'APR Agua Clara',
            'rut' => '76345678-9',
            'direccion' => 'Camino Rural Km 5, Agua Clara',
            'telefono' => '+56967890123',
            'email_contacto' => 'contacto@apraguaclara.cl',
            'slug' => 'aguaclara',
            'dominio_personalizado' => null,
            'estado_dominio_personalizado' => 'sin_configurar',
            'id_suscripcion' => $suscripcionBasico->id,
            'fecha_inicio_suscripcion' => Carbon::now()->subDays(10),
            'fecha_fin_suscripcion' => Carbon::now()->addDays(20), // 30 días prueba
            'estado_suscripcion' => 'prueba',
            'dias_prueba_restantes' => 20,
            'proximo_pago' => Carbon::now()->addDays(20),
            'activo' => true,
            'color_primario' => '#16a085',
            'color_secundario' => '#f39c12',
        ]);

        echo "✓ Organización 3 creada: APR Agua Clara (Básico) - Período de prueba\n";

        Usuario::create([
            'id_organizacion' => $org3->id,
            'nombre_usuario' => 'admin.aguaclara',
            'password' => Hash::make('admin123'),
            'nombre' => 'Pedro',
            'apellido' => 'Sánchez',
            'email' => 'admin@apraguaclara.cl',
            'telefono' => '+56978901234',
            'rol' => 'admin',
            'activo' => true,
        ]);

        echo "  → Admin: admin@apraguaclara.cl / admin123\n";

        // Socios limitados (plan básico tiene límite)
        for ($i = 1; $i <= 5; $i++) {
            Socio::create([
                'id_organizacion' => $org3->id,
                'numero_socio' => 'AC-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'rut' => '1' . rand(1000000, 9999999) . '-' . rand(0, 9),
                'nombre' => 'Cliente ' . $i,
                'apellido_paterno' => 'Clara' . $i,
                'apellido_materno' => 'Prueba' . $i,
                'direccion' => 'Sector Rural ' . $i,
                'sector' => 'Rural',
                'telefono' => '+569' . rand(10000000, 99999999),
                'tipo_cliente' => 'residencial',
                'numero_medidor' => 'MEDAC-' . rand(100, 999),
                'estado' => 'activo',
                'fecha_ingreso' => Carbon::now()->subDays(10),
                'activo' => true,
            ]);
        }

        echo "  → 5 socios creados\n";

        echo "\n=== DATOS DE PRUEBA CREADOS EXITOSAMENTE ===\n\n";
        echo "SUPER ADMIN:\n";
        echo "  Usuario: superadmin\n";
        echo "  Email: superadmin@sistemaapr.cl\n";
        echo "  Password: admin123\n\n";

        echo "ORGANIZACIONES:\n\n";

        echo "1. APR Las Rosas (Profesional - Activa)\n";
        echo "   URL: http://lasrosas.sistemaapr.cl\n";
        echo "   Admin: admin@aprlasrosas.cl / admin123\n";
        echo "   Tesorero: tesoreria@aprlasrosas.cl / tesorero123\n";
        echo "   Socios: 10\n\n";

        echo "2. APR El Valle (Enterprise - Activa - Dominio Verificado)\n";
        echo "   URL: http://elvalle.sistemaapr.cl\n";
        echo "   URL Personalizada: http://www.aprelvalle.test (DNS verificado)\n";
        echo "   Admin: admin@aprelvalle.cl / admin123\n";
        echo "   Operador: operador@aprelvalle.cl / operador123\n";
        echo "   Socios: 15\n\n";

        echo "3. APR Agua Clara (Básico - Prueba - 20 días restantes)\n";
        echo "   URL: http://aguaclara.sistemaapr.cl\n";
        echo "   Admin: admin@apraguaclara.cl / admin123\n";
        echo "   Socios: 5\n\n";
    }
}
