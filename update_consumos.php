<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

// Actualizar todos los consumos
DB::statement('UPDATE lecturas SET consumo_m3 = lectura_actual - lectura_anterior');

echo "✓ Consumos actualizados correctamente\n";
