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
            // Índice para fecha_emision_dte (ordena DTEs por fecha)
            $table->index('fecha_emision_dte', 'idx_boletas_fecha_emision_dte');

            // Índice compuesto para consultas del dashboard DTE (filtra por org + ordena por fecha)
            $table->index(['id_organizacion', 'fecha_emision_dte'], 'idx_boletas_org_fecha_dte');

            // Índice compuesto para búsqueda de DTEs activos por organización
            $table->index(['id_organizacion', 'estado_dte'], 'idx_boletas_org_estado_dte');

            // Índice compuesto para boletas pendientes por mes (usado en vistas)
            $table->index(['mes', 'estado'], 'idx_boletas_mes_estado');

            // Índice para referencias de notas (NC/ND)
            $table->index('boleta_referencia_id', 'idx_boletas_referencia');
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
            // Eliminar índices en orden inverso
            $table->dropIndex('idx_boletas_referencia');
            $table->dropIndex('idx_boletas_mes_estado');
            $table->dropIndex('idx_boletas_org_estado_dte');
            $table->dropIndex('idx_boletas_org_fecha_dte');
            $table->dropIndex('idx_boletas_fecha_emision_dte');
        });
    }
};
