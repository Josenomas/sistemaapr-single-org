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
        Schema::create('historial_consumo', function (Blueprint $table) {
            $table->integer('id', true);
            $table->unsignedBigInteger('id_organizacion')->nullable()->index();
            $table->integer('id_socio')->index('idx_id_socio');
            $table->integer('id_lectura')->index('idx_id_lectura');
            $table->string('periodo', 7)->index('idx_periodo')->comment('Formato: YYYY-MM');
            $table->decimal('lectura_anterior', 10)->default(0);
            $table->decimal('lectura_actual', 10);
            $table->decimal('consumo_m3', 10)->comment('Metros c·bicos consumidos');
            $table->decimal('monto_consumo', 10)->default(0);
            $table->decimal('promedio_diario', 10)->nullable()->comment('Promedio diario en m3');
            $table->enum('anomalia', ['normal', 'alto', 'bajo', 'cero'])->default('normal')->index('idx_anomalia');
            $table->text('observaciones')->nullable();
            $table->boolean('activo')->default(true)->index('idx_activo');
            $table->timestamp('fecha_creacion')->useCurrent();
            $table->timestamp('fecha_actualizacion')->useCurrentOnUpdate()->useCurrent();

            $table->unique(['id_socio', 'periodo'], 'unique_socio_periodo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('historial_consumo');
    }
};
