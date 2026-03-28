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
        Schema::create('sueldos', function (Blueprint $table) {
            $table->integer('id', true);
            $table->unsignedBigInteger('id_organizacion')->nullable()->index();
            $table->integer('id_funcionario')->index('idx_id_funcionario');
            $table->string('periodo', 7)->index('idx_periodo')->comment('Formato: YYYY-MM');
            $table->decimal('sueldo_base', 10);
            $table->decimal('bonos', 10)->nullable()->default(0);
            $table->decimal('descuentos', 10)->nullable()->default(0);
            $table->decimal('total_liquido', 10);
            $table->date('fecha_pago')->index('idx_fecha_pago');
            $table->enum('metodo_pago', ['efectivo', 'transferencia', 'cheque'])->default('transferencia');
            $table->string('comprobante')->nullable();
            $table->text('observaciones')->nullable();
            $table->enum('estado', ['pendiente', 'pagado', 'anulado'])->default('pendiente')->index('idx_estado');
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
        Schema::dropIfExists('sueldos');
    }
};
