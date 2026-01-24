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
        Schema::create('folios_sii', function (Blueprint $table) {
            $table->id();
            $table->string('tipo_documento', 50)->default('boleta'); // boleta, factura, etc
            $table->integer('folio_desde'); // Folio inicial del rango
            $table->integer('folio_hasta'); // Folio final del rango
            $table->integer('folio_actual'); // Folio actual en uso
            $table->date('fecha_autorizacion'); // Fecha de autorización SII
            $table->date('fecha_vencimiento'); // Fecha vencimiento del CAF
            $table->text('caf_xml')->nullable(); // XML del CAF (Código de Autorización de Folios)
            $table->string('estado', 20)->default('activo'); // activo, agotado, vencido
            $table->integer('folios_disponibles')->default(0); // Cantidad de folios disponibles
            $table->unsignedInteger('id_usuario_carga'); // Usuario que cargó el folio
            $table->text('observaciones')->nullable();
            $table->boolean('activo')->default(true);

            $table->timestamp('fecha_creacion')->useCurrent();
            $table->timestamp('fecha_actualizacion')->useCurrent()->useCurrentOnUpdate();

            // Índices
            $table->index('tipo_documento');
            $table->index('estado');
            $table->index('folio_actual');
            $table->index('id_usuario_carga');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('folios_sii');
    }
};
