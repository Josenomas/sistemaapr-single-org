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
        Schema::create('actividad_reciente', function (Blueprint $table) {
            $table->integer('id');
            $table->string('modulo', 50);
            $table->string('descripcion');
            $table->integer('id_usuario')->nullable();
            $table->timestamp('fecha_creacion')->useCurrent();
            $table->boolean('activo')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('actividad_reciente');
    }
};
