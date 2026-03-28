<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Verificar si ya existe un super-admin
        $superAdminExiste = Usuario::where('rol', 'superadmin')->exists();

        if (!$superAdminExiste) {
            Usuario::create([
                'id_organizacion' => null, // Super-admin no pertenece a ninguna organización
                'nombre_usuario' => 'superadmin',
                'password' => Hash::make('SuperAdmin2026!'), // Cambiar en producción
                'nombre' => 'Super',
                'apellido' => 'Administrador',
                'email' => 'superadmin@sistemaapr.cl',
                'telefono' => null,
                'rol' => 'superadmin',
                'permisos' => null,
                'activo' => true,
            ]);

            $this->command->info('✓ Super-admin creado exitosamente');
            $this->command->info('  Usuario: superadmin');
            $this->command->info('  Contraseña: SuperAdmin2026!');
            $this->command->warn('  ⚠️  CAMBIAR CONTRASEÑA EN PRODUCCIÓN');
        } else {
            $this->command->warn('✗ Super-admin ya existe, saltando...');
        }
    }
}
