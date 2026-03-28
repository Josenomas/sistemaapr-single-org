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
        Schema::create('ticket_respuestas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_organizacion')->nullable()->index();
            $table->unsignedInteger('id_ticket')->index();
            $table->unsignedInteger('id_usuario')->nullable();
            $table->unsignedInteger('id_socio')->nullable();
            $table->text('mensaje');
            $table->enum('tipo_autor', ['funcionario', 'socio', 'sistema'])->default('funcionario');
            $table->boolean('visible_socio')->default(true);
            $table->boolean('notificado')->default(false);
            $table->timestamp('fecha_creacion')->useCurrent()->index();
            $table->timestamp('fecha_actualizacion')->useCurrentOnUpdate()->useCurrent();
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
        Schema::dropIfExists('ticket_respuestas');
    }
};
