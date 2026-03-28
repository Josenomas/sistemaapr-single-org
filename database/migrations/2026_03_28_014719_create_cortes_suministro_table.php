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
        Schema::create('cortes_suministro', function (Blueprint $table) {
            $table->integer('id', true);
            $table->unsignedBigInteger('id_organizacion')->nullable()->index();
            $table->integer('id_socio')->index('idx_id_socio');
            $table->enum('motivo', ['morosidad', 'solicitud_socio', 'mantenimiento', 'otro'])->default('morosidad')->index('idx_motivo');
            $table->text('descripcion')->nullable();
            $table->date('fecha_corte')->index('idx_fecha_corte');
            $table->date('fecha_reconexion')->nullable();
            $table->enum('estado', ['pendiente', 'ejecutado', 'reconectado', 'cancelado'])->default('pendiente')->index('idx_estado');
            $table->decimal('monto_adeudado', 10)->nullable();
            $table->decimal('monto_reconexion', 10)->nullable();
            $table->integer('id_ejecutor')->nullable()->index('idx_id_ejecutor')->comment('ID del funcionario que ejecut¾ el corte');
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
        Schema::dropIfExists('cortes_suministro');
    }
};
