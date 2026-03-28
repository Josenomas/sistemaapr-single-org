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
        Schema::create('suscripciones', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre', 50);
            $table->string('nombre_mostrar', 100);
            $table->decimal('precio_mensual', 10)->nullable();
            $table->integer('max_socios')->nullable();
            $table->integer('max_usuarios')->nullable();
            $table->json('modulos_permitidos');
            $table->json('features');
            $table->boolean('permite_dominio_personalizado')->default(false);
            $table->boolean('permite_modulo_noticias')->default(false);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('suscripciones');
    }
};
