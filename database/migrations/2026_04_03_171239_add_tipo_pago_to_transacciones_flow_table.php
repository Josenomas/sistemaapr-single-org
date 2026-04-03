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
        Schema::table('transacciones_flow', function (Blueprint $table) {
            $table->string('tipo_pago', 50)->default('boleta')->after('estado')->comment('Tipo de pago: boleta, suscripcion, cambio_plan');
            $table->unsignedBigInteger('referencia_id')->nullable()->after('tipo_pago')->comment('ID de referencia según tipo_pago');
            $table->index(['tipo_pago', 'referencia_id'], 'idx_tipo_referencia');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transacciones_flow', function (Blueprint $table) {
            $table->dropIndex('idx_tipo_referencia');
            $table->dropColumn(['tipo_pago', 'referencia_id']);
        });
    }
};
