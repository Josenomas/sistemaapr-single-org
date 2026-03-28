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
        Schema::create('incidentes', function (Blueprint $table) {
            $table->integer('id', true);
            $table->unsignedBigInteger('id_organizacion')->nullable()->index();
            $table->enum('tipo', ['fuga', 'corte', 'baja_presion', 'contaminacion', 'otro'])->index('idx_tipo');
            $table->text('descripcion');
            $table->string('ubicacion');
            $table->string('sector', 100)->nullable()->index('idx_sector');
            $table->integer('id_socio_reporta')->nullable()->index('fk_incidentes_socio');
            $table->enum('prioridad', ['baja', 'media', 'alta', 'critica'])->default('media')->index('idx_prioridad');
            $table->enum('estado', ['reportado', 'en_atencion', 'resuelto', 'cerrado'])->default('reportado')->index('idx_estado');
            $table->dateTime('fecha_reporte')->useCurrent()->index('idx_fecha_reporte');
            $table->dateTime('fecha_atencion')->nullable();
            $table->dateTime('fecha_resolucion')->nullable();
            $table->text('solucion')->nullable();
            $table->text('observaciones')->nullable();
            $table->integer('id_usuario_asignado')->nullable()->index('fk_incidentes_usuario');
            $table->timestamp('fecha_creacion')->useCurrent();

            $table->index(['sector', 'estado'], 'idx_incidentes_sector_estado');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('incidentes');
    }
};
