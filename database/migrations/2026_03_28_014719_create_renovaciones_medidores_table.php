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
        Schema::create('renovaciones_medidores', function (Blueprint $table) {
            $table->integer('id', true);
            $table->unsignedBigInteger('id_organizacion')->nullable()->index();
            $table->integer('id_socio')->index('idx_id_socio');
            $table->string('medidor_anterior', 100)->nullable();
            $table->decimal('lectura_anterior', 10)->nullable();
            $table->string('medidor_nuevo', 100);
            $table->decimal('lectura_inicial', 10)->default(0);
            $table->date('fecha_renovacion')->index('idx_fecha_renovacion');
            $table->enum('motivo', ['deterioro', 'falla', 'actualizacion', 'robo', 'otro'])->default('deterioro')->index('idx_motivo');
            $table->decimal('costo_renovacion', 10)->nullable();
            $table->integer('id_tecnico')->nullable()->index('idx_id_tecnico')->comment('ID del funcionario tÚcnico');
            $table->text('observaciones')->nullable();
            $table->enum('estado', ['planificado', 'ejecutado', 'cancelado'])->default('planificado')->index('idx_estado');
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
        Schema::dropIfExists('renovaciones_medidores');
    }
};
