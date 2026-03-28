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
        Schema::create('boletas', function (Blueprint $table) {
            $table->integer('id', true);
            $table->unsignedBigInteger('id_organizacion')->nullable()->index();
            $table->string('numero_boleta', 50)->index('idx_numero_boleta');
            $table->integer('id_socio')->index('idx_socio');
            $table->integer('id_lectura')->nullable()->index('idx_lectura');
            $table->string('mes', 7)->index('idx_mes')->comment('Formato: YYYY-MM');
            $table->date('fecha_emision');
            $table->date('fecha_vencimiento')->index('idx_fecha_vencimiento');
            $table->decimal('consumo_m3', 10)->default(0);
            $table->decimal('cargo_fijo', 10)->default(0);
            $table->decimal('cargo_consumo', 10)->default(0);
            $table->decimal('otros_cargos', 10)->default(0);
            $table->decimal('descuentos', 10)->default(0);
            $table->decimal('subsidio', 10)->default(0);
            $table->decimal('total', 10);
            $table->enum('estado', ['pendiente', 'pagada', 'vencida', 'anulada'])->default('pendiente')->index('idx_estado');
            $table->text('observaciones')->nullable();
            $table->boolean('activo')->default(true)->index('idx_activo');
            $table->timestamp('fecha_creacion')->useCurrent();
            $table->timestamp('fecha_actualizacion')->useCurrentOnUpdate()->useCurrent();

            $table->index(['id_socio', 'estado'], 'idx_boletas_socio_estado');
            $table->unique(['numero_boleta'], 'numero_boleta');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('boletas');
    }
};
