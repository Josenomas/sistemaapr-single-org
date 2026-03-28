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
        Schema::create('mantenciones', function (Blueprint $table) {
            $table->integer('id', true);
            $table->unsignedBigInteger('id_organizacion')->nullable()->index();
            $table->enum('tipo', ['preventiva', 'correctiva', 'emergencia'])->index('idx_tipo');
            $table->text('descripcion');
            $table->string('ubicacion');
            $table->date('fecha_programada')->nullable()->index('idx_fecha_programada');
            $table->date('fecha_realizada')->nullable();
            $table->decimal('costo', 10)->nullable()->default(0);
            $table->string('responsable', 150)->nullable();
            $table->enum('estado', ['programada', 'en_proceso', 'completada', 'cancelada'])->default('programada')->index('idx_estado');
            $table->text('observaciones')->nullable();
            $table->integer('id_usuario_registro')->nullable()->index('fk_mantenciones_usuario');
            $table->timestamp('fecha_creacion')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mantenciones');
    }
};
