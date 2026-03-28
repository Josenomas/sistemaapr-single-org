<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_organizacion')->nullable();
            $table->string('nombre_usuario', 100)->unique();
            $table->string('password');
            $table->string('nombre', 100);
            $table->string('apellido', 100);
            $table->string('email', 150)->unique();
            $table->string('telefono', 20)->nullable();
            $table->enum('rol', ['admin', 'tesorero', 'operador', 'lecturista', 'superadmin'])->default('operador');
            $table->json('permisos')->nullable(); // Para permisos granulares por módulo
            $table->boolean('activo')->default(true);
            $table->timestamp('ultimo_acceso')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index('id_organizacion');
            $table->index('rol');
            $table->index('activo');

            // Foreign key
            $table->foreign('id_organizacion')->references('id')->on('organizaciones')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
