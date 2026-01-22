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
            $table->id();
            $table->string('codigo_rendicion', 50)->unique();
            $table->string('periodo', 7); // Formato: YYYY-MM
            $table->integer('mes');
            $table->integer('anio');

            // Saldos
            $table->decimal('saldo_anterior', 12, 2)->default(0);
            $table->decimal('total_ingresos', 12, 2)->default(0);
            $table->decimal('total_egresos', 12, 2)->default(0);
            $table->decimal('saldo_final', 12, 2)->default(0);

            // Desglose de Ingresos
            $table->decimal('ingresos_consumo_agua', 12, 2)->default(0);
            $table->decimal('ingresos_subsidios', 12, 2)->default(0);
            $table->decimal('ingresos_aportes_socios', 12, 2)->default(0);
            $table->decimal('ingresos_multas', 12, 2)->default(0);
            $table->decimal('ingresos_incorporaciones', 12, 2)->default(0);
            $table->decimal('ingresos_otros', 12, 2)->default(0);

            // Desglose de Egresos
            $table->decimal('egresos_energia_electrica', 12, 2)->default(0);
            $table->decimal('egresos_productos_quimicos', 12, 2)->default(0);
            $table->decimal('egresos_reparaciones', 12, 2)->default(0);
            $table->decimal('egresos_remuneraciones', 12, 2)->default(0);
            $table->decimal('egresos_gastos_administrativos', 12, 2)->default(0);
            $table->decimal('egresos_otros', 12, 2)->default(0);

            // Control y auditoría
            $table->enum('estado', ['abierto', 'cerrado'])->default('abierto');
            $table->timestamp('fecha_cierre')->nullable();
            $table->foreignId('id_usuario_cierre')->nullable()->constrained('users')->onDelete('set null');

            // Notas y observaciones
            $table->text('observaciones')->nullable();
            $table->text('notas_cierre')->nullable();

            // Responsable de la rendición
            $table->foreignId('id_responsable')->nullable()->constrained('users')->onDelete('set null');

            // Metadatos
            $table->boolean('activo')->default(true);
            $table->timestamp('fecha_creacion')->useCurrent();
            $table->timestamp('fecha_actualizacion')->useCurrent()->useCurrentOnUpdate();

            // Índices
            $table->index('periodo');
            $table->index('estado');
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
