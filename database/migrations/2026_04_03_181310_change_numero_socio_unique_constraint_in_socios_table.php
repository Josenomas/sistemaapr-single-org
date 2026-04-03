<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('socios', function (Blueprint $table) {
            // Eliminar la restricción unique global de numero_socio
            $table->dropUnique('numero_socio');

            // Crear restricción unique compuesta para numero_socio + id_organizacion
            // Esto permite que diferentes organizaciones tengan el mismo numero_socio
            $table->unique(['id_organizacion', 'numero_socio'], 'unique_numero_socio_por_organizacion');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('socios', function (Blueprint $table) {
            // Revertir: eliminar la restricción compuesta
            $table->dropUnique('unique_numero_socio_por_organizacion');

            // Restaurar la restricción unique global original
            $table->unique(['numero_socio'], 'numero_socio');
        });
    }
};
