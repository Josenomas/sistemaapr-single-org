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
        Schema::create('trabajos_realizados', function (Blueprint $table) {
            $table->integer('id', true);
            $table->unsignedBigInteger('id_organizacion')->nullable()->index();
            $table->string('titulo', 200);
            $table->text('descripcion');
            $table->enum('tipo_trabajo', ['mantenimiento', 'reparacion', 'instalacion', 'inspeccion', 'otro'])->default('mantenimiento')->index('idx_tipo_trabajo');
            $table->string('ubicacion')->nullable();
            $table->date('fecha_inicio')->index('idx_fecha_inicio');
            $table->date('fecha_termino')->nullable();
            $table->enum('estado', ['planificado', 'en_proceso', 'completado', 'cancelado'])->default('planificado')->index('idx_estado');
            $table->enum('prioridad', ['baja', 'media', 'alta', 'urgente'])->default('media')->index('idx_prioridad');
            $table->decimal('costo_estimado', 10)->nullable();
            $table->decimal('costo_real', 10)->nullable();
            $table->integer('id_responsable')->nullable()->index('idx_id_responsable')->comment('ID del funcionario responsable');
            $table->text('materiales_utilizados')->nullable();
            $table->text('observaciones')->nullable();
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
        Schema::dropIfExists('trabajos_realizados');
    }
};
