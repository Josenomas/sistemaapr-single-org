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
        Schema::create('reclamos', function (Blueprint $table) {
            $table->id();
            $table->string('numero_reclamo')->unique(); // REC-0001, REC-0002...
            $table->unsignedBigInteger('id_organizacion')->nullable();

            // Datos del reclamante
            $table->string('nombre_completo');
            $table->string('rut', 12);
            $table->string('email');
            $table->string('telefono', 20)->nullable();
            $table->text('direccion')->nullable();

            // Datos del reclamo
            $table->enum('tipo_reclamo', ['servicio', 'facturacion', 'soporte', 'funcionalidad', 'otro']);
            $table->text('detalle_reclamo');
            $table->text('solucion_solicitada')->nullable();

            // Gestión del reclamo
            $table->enum('estado', ['pendiente', 'en_revision', 'resuelto', 'rechazado'])->default('pendiente');
            $table->text('respuesta')->nullable();
            $table->timestamp('fecha_respuesta')->nullable();
            $table->unsignedBigInteger('respondido_por')->nullable(); // ID del usuario que respondió

            // Metadatos
            $table->string('ip_address', 45);
            $table->text('user_agent')->nullable();

            $table->timestamps();

            $table->foreign('id_organizacion')->references('id')->on('organizaciones')->onDelete('set null');
            $table->foreign('respondido_por')->references('id')->on('usuarios')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reclamos');
    }
};
