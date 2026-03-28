<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('socios', function (Blueprint $table) {
            // Subsidio por porcentaje (ej: subsidio municipal del 50%)
            $table->decimal('subsidio_porcentaje', 5, 2)->default(0)->after('exento_iva')
                ->comment('Porcentaje de subsidio a aplicar (ej: 50 = 50%)');

            // Descuento por monto fijo (ej: convenio de $3,000)
            $table->decimal('descuento_monto', 10, 2)->default(0)->after('subsidio_porcentaje')
                ->comment('Monto fijo de descuento (ej: convenio especial)');

            // Descripción del subsidio/descuento
            $table->string('observaciones_subsidio', 255)->nullable()->after('descuento_monto')
                ->comment('Descripción del subsidio o convenio (ej: Subsidio Municipal, Convenio APR)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('socios', function (Blueprint $table) {
            $table->dropColumn(['subsidio_porcentaje', 'descuento_monto', 'observaciones_subsidio']);
        });
    }
};
