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
        Schema::create('eventos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_organizacion')->nullable()->index();
            $table->string('titulo');
            $table->string('tipo', 100)->index();
            $table->text('descripcion')->nullable();
            $table->date('fecha_evento')->index();
            $table->enum('recurrencia', ['ninguna', 'diaria', 'semanal', 'mensual', 'anual'])->default('ninguna');
            $table->integer('dia_recurrencia')->nullable();
            $table->string('icono', 100)->default('fa-calendar-check');
            $table->enum('color', ['primary', 'success', 'warning', 'danger', 'info'])->default('primary');
            $table->boolean('notificar')->default(false);
            $table->integer('dias_notificacion')->nullable();
            $table->boolean('activo')->default(true)->index();
            $table->timestamp('fecha_creacion')->useCurrent();
            $table->timestamp('fecha_actualizacion')->useCurrentOnUpdate()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('eventos');
    }
};
