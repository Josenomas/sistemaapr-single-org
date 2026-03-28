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
        Schema::create('recordatorios', function (Blueprint $table) {
            $table->integer('id', true);
            $table->unsignedBigInteger('id_organizacion')->nullable()->index();
            $table->string('titulo', 200);
            $table->text('descripcion');
            $table->enum('tipo_recordatorio', ['reunion', 'pago', 'mantenimiento', 'inspeccion', 'vencimiento', 'llamada', 'tarea', 'otro'])->default('tarea')->index('idx_tipo_recordatorio');
            $table->enum('prioridad', ['baja', 'media', 'alta', 'urgente'])->default('media')->index('idx_prioridad');
            $table->date('fecha_recordatorio')->index('idx_fecha_recordatorio');
            $table->time('hora_recordatorio')->nullable();
            $table->date('fecha_vencimiento')->nullable();
            $table->enum('estado', ['pendiente', 'completado', 'cancelado', 'vencido'])->default('pendiente')->index('idx_estado');
            $table->integer('id_asignado')->nullable()->index('idx_id_asignado')->comment('ID del funcionario asignado');
            $table->integer('id_relacionado')->nullable()->comment('ID relacionado (socio, ticket, etc)');
            $table->string('tipo_relacionado', 50)->nullable()->comment('Tipo de entidad relacionada');
            $table->string('ubicacion')->nullable();
            $table->text('notas')->nullable();
            $table->date('fecha_completado')->nullable();
            $table->boolean('notificado')->default(false);
            $table->boolean('activo')->default(true)->index('idx_activo');
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
        Schema::dropIfExists('recordatorios');
    }
};
