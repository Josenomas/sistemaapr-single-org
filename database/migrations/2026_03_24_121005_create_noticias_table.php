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
        Schema::create('noticias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_organizacion')->constrained('organizaciones')->onDelete('cascade');
            $table->string('titulo', 200);
            $table->string('slug', 250)->unique();
            $table->text('resumen')->nullable();
            $table->longText('contenido');
            $table->string('imagen_destacada', 255)->nullable();
            $table->enum('categoria', ['aviso', 'evento', 'mantenimiento', 'corte', 'reunion', 'otro'])->default('aviso');
            $table->enum('estado', ['borrador', 'publicada', 'archivada'])->default('borrador');
            $table->boolean('destacada')->default(false); // Para mostrar en slider principal
            $table->dateTime('fecha_publicacion')->nullable();
            $table->foreignId('id_usuario_creador')->constrained('users')->onDelete('restrict');
            $table->integer('vistas')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['id_organizacion', 'estado', 'fecha_publicacion']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('noticias');
    }
};
