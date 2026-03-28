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
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_organizacion')->nullable();
            $table->unsignedBigInteger('id_usuario')->nullable();
            $table->string('modulo');
            $table->string('accion');
            $table->string('tabla_afectada')->nullable();
            $table->unsignedBigInteger('id_registro')->nullable();
            $table->text('descripcion');
            $table->json('datos_anteriores')->nullable();
            $table->json('datos_nuevos')->nullable();
            $table->string('ip_address', 45);
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->index(['id_organizacion', 'created_at']);
            $table->index(['modulo', 'accion']);
            $table->index(['id_usuario', 'created_at']);
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
