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
        Schema::create('cambios_plan', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_organizacion')->index();
            $table->unsignedBigInteger('id_suscripcion_anterior')->index('cambios_plan_id_suscripcion_anterior_foreign');
            $table->unsignedBigInteger('id_suscripcion_nueva')->index('cambios_plan_id_suscripcion_nueva_foreign');
            $table->enum('tipo', ['upgrade', 'downgrade'])->comment('Tipo de cambio');
            $table->enum('estado', ['pendiente', 'procesando', 'completado', 'rechazado', 'cancelado'])->default('pendiente')->index();
            $table->decimal('monto_anterior', 10);
            $table->decimal('monto_nuevo', 10);
            $table->decimal('monto_diferencia', 10)->comment('Diferencia prorrateada a pagar/devolver');
            $table->string('token_flow')->nullable()->index()->comment('Token de transacción Flow');
            $table->unsignedInteger('id_transaccion_flow')->nullable();
            $table->timestamp('fecha_solicitud')->useCurrent()->index();
            $table->timestamp('fecha_aplicacion')->nullable()->comment('Fecha en que se aplicó el cambio');
            $table->text('observaciones')->nullable();
            $table->boolean('activo')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cambios_plan');
    }
};
