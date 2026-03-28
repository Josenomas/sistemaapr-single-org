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
        Schema::create('auditoria', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_organizacion')->nullable(); // NULL para acciones del super-admin
            $table->unsignedBigInteger('id_usuario')->nullable();
            $table->string('modulo'); // socios, boletas, usuarios, etc.
            $table->string('accion'); // crear, editar, eliminar, login, logout
            $table->string('tabla_afectada')->nullable();
            $table->unsignedBigInteger('id_registro')->nullable();
            $table->text('descripcion');
            $table->json('datos_anteriores')->nullable(); // JSON con datos antes del cambio
            $table->json('datos_nuevos')->nullable(); // JSON con datos después del cambio
            $table->string('ip_address', 45);
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->foreign('id_organizacion')->references('id')->on('organizaciones')->onDelete('cascade');
            $table->foreign('id_usuario')->references('id')->on('usuarios')->onDelete('set null');

            $table->index(['id_organizacion', 'created_at']);
            $table->index(['id_usuario', 'created_at']);
            $table->index(['modulo', 'accion']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('auditoria');
    }
};
