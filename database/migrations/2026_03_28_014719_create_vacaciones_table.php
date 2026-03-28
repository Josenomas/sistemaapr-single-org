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
        Schema::create('vacaciones', function (Blueprint $table) {
            $table->integer('id', true);
            $table->unsignedBigInteger('id_organizacion')->nullable()->index();
            $table->integer('id_funcionario')->index('idx_id_funcionario');
            $table->date('fecha_inicio')->index('idx_fecha_inicio');
            $table->date('fecha_termino');
            $table->integer('dias_habiles');
            $table->string('periodo', 4)->index('idx_periodo')->comment('A±o al que corresponden las vacaciones');
            $table->enum('tipo', ['legales', 'progresivas', 'administrativas', 'sin_goce'])->default('legales')->index('idx_tipo');
            $table->enum('estado', ['solicitada', 'aprobada', 'rechazada', 'en_curso', 'finalizada', 'cancelada'])->default('solicitada')->index('idx_estado');
            $table->date('fecha_solicitud');
            $table->date('fecha_aprobacion')->nullable();
            $table->integer('id_aprobador')->nullable()->index('idx_id_aprobador')->comment('ID del funcionario que aprob¾');
            $table->text('motivo_rechazo')->nullable();
            $table->text('observaciones')->nullable();
            $table->integer('suplente')->nullable()->index('idx_suplente')->comment('ID del funcionario suplente');
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
        Schema::dropIfExists('vacaciones');
    }
};
