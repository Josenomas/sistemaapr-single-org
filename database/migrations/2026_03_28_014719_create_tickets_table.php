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
        Schema::create('tickets', function (Blueprint $table) {
            $table->integer('id', true);
            $table->unsignedBigInteger('id_organizacion')->nullable()->index();
            $table->string('numero_ticket', 50)->unique('numero_ticket');
            $table->integer('id_socio')->nullable()->index('idx_id_socio')->comment('ID del socio que reporta');
            $table->string('titulo', 200);
            $table->text('descripcion');
            $table->enum('tipo_ticket', ['consulta', 'reclamo', 'solicitud', 'averia', 'fuga', 'corte', 'reconexion', 'lectura', 'otro'])->default('consulta')->index('idx_tipo_ticket');
            $table->enum('prioridad', ['baja', 'media', 'alta', 'urgente'])->default('media')->index('idx_prioridad');
            $table->enum('estado', ['abierto', 'en_proceso', 'pendiente', 'resuelto', 'cerrado', 'cancelado'])->default('abierto')->index('idx_estado');
            $table->integer('id_asignado')->nullable()->index('idx_id_asignado')->comment('ID del funcionario asignado');
            $table->date('fecha_reporte')->index('idx_fecha_reporte');
            $table->date('fecha_asignacion')->nullable();
            $table->date('fecha_resolucion')->nullable();
            $table->date('fecha_cierre')->nullable();
            $table->integer('tiempo_respuesta')->nullable()->comment('Minutos hasta primera respuesta');
            $table->integer('tiempo_resolucion')->nullable()->comment('Minutos hasta resoluci¾n');
            $table->text('solucion')->nullable();
            $table->decimal('costo_reparacion', 10)->nullable();
            $table->enum('satisfaccion', ['muy_insatisfecho', 'insatisfecho', 'neutral', 'satisfecho', 'muy_satisfecho'])->nullable();
            $table->text('comentario_cierre')->nullable();
            $table->string('ubicacion')->nullable();
            $table->string('contacto_nombre', 100)->nullable();
            $table->string('contacto_telefono', 20)->nullable();
            $table->string('contacto_email', 150)->nullable();
            $table->text('observaciones')->nullable();
            $table->boolean('activo')->default(true)->index('idx_activo');
            $table->timestamp('fecha_creacion')->useCurrent();
            $table->timestamp('fecha_actualizacion')->useCurrentOnUpdate()->useCurrent();

            $table->index(['numero_ticket'], 'idx_numero_ticket');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tickets');
    }
};
