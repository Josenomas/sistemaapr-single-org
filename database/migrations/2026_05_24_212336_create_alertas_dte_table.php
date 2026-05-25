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
        Schema::create('alertas_dte', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_organizacion');

            // Tipo de alerta
            $table->enum('tipo', [
                'sin_folios',
                'folios_bajos',
                'conexion_fallida',
                'dte_rechazado',
                'ambiente_certificacion',
                'libro_ventas_pendiente',
                'configuracion_exitosa',
                'hito_dtes'
            ])->comment('Tipo de alerta');

            // Nivel de severidad
            $table->enum('nivel', ['critico', 'advertencia', 'info'])->default('info');

            // Detalles
            $table->string('titulo', 200);
            $table->text('mensaje');
            $table->json('datos_adicionales')->nullable()->comment('JSON con datos extra');

            // Estado
            $table->boolean('leida')->default(false);
            $table->timestamp('fecha_lectura')->nullable();
            $table->boolean('resuelta')->default(false);
            $table->timestamp('fecha_resolucion')->nullable();

            // Email enviado
            $table->boolean('email_enviado')->default(false);
            $table->timestamp('fecha_email')->nullable();

            $table->timestamps();

            // Índices
            $table->index('id_organizacion');
            $table->index('tipo');
            $table->index('nivel');
            $table->index(['leida', 'resuelta']);
            $table->index('created_at');

            // Foreign key
            $table->foreign('id_organizacion')
                  ->references('id')
                  ->on('organizaciones')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('alertas_dte');
    }
};
