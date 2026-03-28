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
        Schema::create('registros_organizacion', function (Blueprint $table) {
            $table->id();

            // Datos de la organización
            $table->string('nombre_apr');
            $table->string('slug')->unique();
            $table->string('rut', 12)->unique();
            $table->string('direccion')->nullable();
            $table->string('comuna')->nullable();
            $table->string('region')->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('email_contacto')->unique();

            // Datos del usuario administrador
            $table->string('admin_nombre');
            $table->string('admin_apellido');
            $table->string('admin_email')->unique();
            $table->string('admin_telefono')->nullable();
            $table->string('password'); // Ya hasheada

            // Control de registro
            $table->string('token_verificacion')->unique();
            $table->enum('estado', ['pendiente', 'verificado', 'rechazado', 'expirado'])->default('pendiente');
            $table->timestamp('email_verificado_at')->nullable();
            $table->timestamp('expira_en')->nullable();
            $table->string('ip_registro', 45)->nullable();

            // Plan seleccionado (opcional, por defecto será Básico)
            $table->unsignedBigInteger('id_suscripcion_deseada')->nullable();

            // Metadata
            $table->text('notas')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('estado');
            $table->index('token_verificacion');
            $table->index('email_contacto');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('registros_organizacion');
    }
};
