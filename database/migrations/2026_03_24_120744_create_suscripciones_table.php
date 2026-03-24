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
            $table->id();
            $table->string('nombre', 50); // basico, profesional, enterprise
            $table->string('nombre_mostrar', 100); // Básico, Profesional, Enterprise
            $table->decimal('precio_mensual', 10, 2)->nullable();
            $table->integer('max_socios')->nullable(); // null = ilimitado
            $table->integer('max_usuarios')->nullable(); // null = ilimitado
            $table->json('modulos_permitidos'); // ['socios', 'lecturas', 'boletas', ...]
            $table->json('features'); // ['Soporte 24/7', 'Dominio personalizado', ...]
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
