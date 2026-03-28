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
        Schema::create('pagos', function (Blueprint $table) {
            $table->integer('id', true);
            $table->unsignedBigInteger('id_organizacion')->nullable()->index();
            $table->string('numero_recibo', 50)->unique('numero_recibo');
            $table->integer('id_boleta')->index('idx_boleta');
            $table->integer('id_socio')->index('idx_socio');
            $table->date('fecha_pago')->index('idx_pagos_fecha');
            $table->decimal('monto_pagado', 10);
            $table->enum('metodo_pago', ['efectivo', 'transferencia', 'cheque', 'debito', 'credito']);
            $table->string('numero_comprobante', 100)->nullable()->comment('N├║mero de transferencia, cheque, etc.');
            $table->text('observaciones')->nullable();
            $table->integer('id_usuario_registro')->nullable()->index('fk_pagos_usuario');
            $table->timestamp('fecha_creacion')->useCurrent();

            $table->index(['fecha_pago'], 'idx_fecha_pago');
            $table->index(['numero_recibo'], 'idx_numero_recibo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pagos');
    }
};
