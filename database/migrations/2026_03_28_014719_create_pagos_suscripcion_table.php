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
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_organizacion')->index();
            $table->unsignedBigInteger('id_suscripcion');
            $table->decimal('monto', 10);
            $table->enum('metodo_pago', ['flow', 'manual', 'cortesia'])->default('flow');
            $table->enum('estado', ['pendiente', 'pagado', 'fallido', 'reembolsado'])->default('pendiente')->index();
            $table->date('periodo_inicio');
            $table->date('periodo_fin');
            $table->string('token_flow')->nullable();
            $table->unsignedInteger('id_transaccion_flow')->nullable();
            $table->string('orden_compra')->nullable();
            $table->text('notas')->nullable();
            $table->timestamp('fecha_pago')->nullable();
            $table->timestamp('fecha_vencimiento')->nullable()->index();
            $table->timestamps();
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
