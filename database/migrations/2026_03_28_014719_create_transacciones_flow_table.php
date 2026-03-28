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
        Schema::create('transacciones_flow', function (Blueprint $table) {
            $table->integer('id', true);
            $table->unsignedBigInteger('id_organizacion')->nullable()->index();
            $table->integer('flow_order')->unique('flow_order')->comment('N·mero de orden de Flow');
            $table->string('token', 100)->index('idx_token')->comment('Token ·nico de la transacci¾n');
            $table->integer('id_socio')->index('idx_id_socio');
            $table->integer('id_boleta')->nullable()->index('idx_id_boleta')->comment('Boleta asociada al pago');
            $table->decimal('monto', 10);
            $table->string('email', 150);
            $table->string('subject')->comment('Asunto del pago');
            $table->string('url_confirmacion');
            $table->string('url_retorno');
            $table->enum('estado', ['pendiente', 'pagado', 'rechazado', 'anulado', 'expirado'])->default('pendiente')->index('idx_estado');
            $table->integer('flow_status')->nullable()->comment('Status de Flow (1=Pagado, 2=Rechazado, etc)');
            $table->text('payment_data')->nullable()->comment('JSON con datos de confirmaci¾n de Flow');
            $table->text('observaciones')->nullable();
            $table->text('boletas_ids')->nullable();
            $table->timestamp('fecha_creacion')->useCurrent();
            $table->timestamp('fecha_pago')->nullable();
            $table->timestamp('fecha_actualizacion')->useCurrentOnUpdate()->useCurrent();
            $table->boolean('activo')->default(true)->index('idx_activo');

            $table->unique(['token'], 'token');
            $table->index(['flow_order'], 'idx_flow_order');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transacciones_flow');
    }
};
