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
            $table->id();
            $table->string('titulo', 255);
            $table->string('tipo', 100); // FACTURACIÓN, OPERACIÓN, COBRO, MANTENIMIENTO, OTRO
            $table->text('descripcion')->nullable();
            $table->date('fecha_evento');
            $table->enum('recurrencia', ['ninguna', 'diaria', 'semanal', 'mensual', 'anual'])->default('ninguna');
            $table->integer('dia_recurrencia')->nullable(); // Para eventos mensuales (día del mes)
            $table->string('icono', 100)->default('fa-calendar-check');
            $table->enum('color', ['primary', 'success', 'warning', 'danger', 'info'])->default('primary');
            $table->boolean('notificar')->default(false);
            $table->integer('dias_notificacion')->nullable(); // Días antes para notificar
            $table->boolean('activo')->default(true);
            $table->timestamp('fecha_creacion')->useCurrent();
            $table->timestamp('fecha_actualizacion')->useCurrent()->useCurrentOnUpdate();

            $table->index('fecha_evento');
            $table->index('activo');
            $table->index('tipo');
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
