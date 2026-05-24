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
        Schema::table('boletas', function (Blueprint $table) {
            // Campos para Notas de Crédito y Débito
            $table->foreignId('boleta_referencia_id')->nullable()->after('comuna_receptor')
                  ->constrained('boletas')->onDelete('set null')
                  ->comment('ID de la boleta original que se está referenciando (para notas de crédito/débito)');

            $table->string('motivo_nota', 500)->nullable()->after('boleta_referencia_id')
                  ->comment('Motivo de la nota de crédito o débito');

            $table->decimal('monto_nota', 10, 2)->nullable()->after('motivo_nota')
                  ->comment('Monto de la nota (puede ser parcial o total)');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('boletas', function (Blueprint $table) {
            $table->dropForeign(['boleta_referencia_id']);
            $table->dropColumn(['boleta_referencia_id', 'motivo_nota', 'monto_nota']);
        });
    }
};
