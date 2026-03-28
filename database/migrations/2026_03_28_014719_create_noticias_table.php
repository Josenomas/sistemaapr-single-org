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
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_organizacion');
            $table->string('titulo', 200);
            $table->string('slug', 250)->unique();
            $table->text('resumen')->nullable();
            $table->longText('contenido');
            $table->string('imagen_destacada')->nullable();
            $table->string('categoria', 50)->nullable();
            $table->enum('estado', ['borrador', 'publicada', 'archivada'])->default('borrador');
            $table->boolean('destacada')->default(false);
            $table->dateTime('fecha_publicacion')->nullable();
            $table->unsignedBigInteger('id_usuario_creador')->index('noticias_id_usuario_creador_foreign');
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
