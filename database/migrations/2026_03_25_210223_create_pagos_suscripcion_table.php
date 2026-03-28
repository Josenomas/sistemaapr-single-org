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
        Schema::create('pagos_suscripcion', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_organizacion');
            $table->unsignedBigInteger('id_suscripcion');

            // Información del pago
            $table->decimal('monto', 10, 2);
            $table->enum('metodo_pago', ['flow', 'manual', 'cortesia'])->default('flow');
            $table->enum('estado', ['pendiente', 'pagado', 'fallido', 'reembolsado'])->default('pendiente');

            // Período que cubre este pago
            $table->date('periodo_inicio');
            $table->date('periodo_fin');

            // Datos de Flow (si aplica)
            $table->string('token_flow')->nullable();
            $table->unsignedInteger('id_transaccion_flow')->nullable();
            $table->string('orden_compra')->nullable();

            // Metadata
            $table->text('notas')->nullable();
            $table->timestamp('fecha_pago')->nullable();
            $table->timestamp('fecha_vencimiento')->nullable();

            $table->timestamps();

            // Índices
            $table->index('id_organizacion');
            $table->index('estado');
            $table->index('fecha_vencimiento');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pagos_suscripcion');
    }
};
