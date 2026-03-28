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
        Schema::create('rendiciones_mensuales', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_organizacion')->nullable()->index();
            $table->string('codigo_rendicion', 50)->unique();
            $table->string('periodo', 7)->index();
            $table->integer('mes');
            $table->integer('anio');
            $table->decimal('saldo_anterior', 12)->default(0);
            $table->decimal('total_ingresos', 12)->default(0);
            $table->decimal('total_egresos', 12)->default(0);
            $table->decimal('saldo_final', 12)->default(0);
            $table->decimal('ingresos_consumo_agua', 12)->default(0);
            $table->decimal('ingresos_subsidios', 12)->default(0);
            $table->decimal('ingresos_aportes_socios', 12)->default(0);
            $table->decimal('ingresos_multas', 12)->default(0);
            $table->decimal('ingresos_incorporaciones', 12)->default(0);
            $table->decimal('ingresos_otros', 12)->default(0);
            $table->decimal('egresos_energia_electrica', 12)->default(0);
            $table->decimal('egresos_productos_quimicos', 12)->default(0);
            $table->decimal('egresos_reparaciones', 12)->default(0);
            $table->decimal('egresos_remuneraciones', 12)->default(0);
            $table->decimal('egresos_gastos_administrativos', 12)->default(0);
            $table->decimal('egresos_otros', 12)->default(0);
            $table->enum('estado', ['abierto', 'cerrado'])->default('abierto')->index();
            $table->timestamp('fecha_cierre')->nullable();
            $table->unsignedBigInteger('id_usuario_cierre')->nullable()->index('rendiciones_mensuales_id_usuario_cierre_foreign');
            $table->text('observaciones')->nullable();
            $table->text('notas_cierre')->nullable();
            $table->unsignedBigInteger('id_responsable')->nullable()->index('rendiciones_mensuales_id_responsable_foreign');
            $table->boolean('activo')->default(true);
            $table->timestamp('fecha_creacion')->useCurrent();
            $table->timestamp('fecha_actualizacion')->useCurrentOnUpdate()->useCurrent();

            $table->index(['mes', 'anio']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rendiciones_mensuales');
    }
};
