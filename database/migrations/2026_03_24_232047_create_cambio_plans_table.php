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
            $table->id();
            $table->unsignedBigInteger('id_organizacion');
            $table->unsignedBigInteger('id_suscripcion_anterior');
            $table->unsignedBigInteger('id_suscripcion_nueva');
            $table->enum('tipo', ['upgrade', 'downgrade'])->comment('Tipo de cambio');
            $table->enum('estado', ['pendiente', 'procesando', 'completado', 'rechazado', 'cancelado'])->default('pendiente');
            $table->decimal('monto_anterior', 10, 2);
            $table->decimal('monto_nuevo', 10, 2);
            $table->decimal('monto_diferencia', 10, 2)->comment('Diferencia prorrateada a pagar/devolver');
            $table->string('token_flow')->nullable()->comment('Token de transacción Flow');
            $table->unsignedInteger('id_transaccion_flow')->nullable();
            $table->timestamp('fecha_solicitud')->useCurrent();
            $table->timestamp('fecha_aplicacion')->nullable()->comment('Fecha en que se aplicó el cambio');
            $table->text('observaciones')->nullable();
            $table->boolean('activo')->default(true);

            // Foreign keys
            $table->foreign('id_organizacion')->references('id')->on('organizaciones')->onDelete('cascade');
            $table->foreign('id_suscripcion_anterior')->references('id')->on('suscripciones')->onDelete('restrict');
            $table->foreign('id_suscripcion_nueva')->references('id')->on('suscripciones')->onDelete('restrict');

            // Indexes
            $table->index('id_organizacion');
            $table->index('estado');
            $table->index('token_flow');
            $table->index('fecha_solicitud');
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
