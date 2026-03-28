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
            $table->id();
            $table->bigInteger('flow_order')->nullable();
            $table->string('token')->nullable();
            $table->unsignedBigInteger('id_socio')->nullable();
            $table->unsignedBigInteger('id_boleta')->nullable();
            $table->decimal('monto', 10, 2);
            $table->string('email')->nullable();
            $table->string('subject')->nullable();
            $table->text('url_confirmacion')->nullable();
            $table->text('url_retorno')->nullable();
            $table->string('estado')->default('pendiente');
            $table->integer('flow_status')->nullable();
            $table->text('payment_data')->nullable();
            $table->timestamp('fecha_pago')->nullable();
            $table->text('observaciones')->nullable();
            $table->text('boletas_ids')->nullable();
            $table->boolean('activo')->default(1);
            $table->timestamp('fecha_creacion')->nullable();
            $table->timestamp('fecha_actualizacion')->nullable();

            $table->foreign('id_socio')->references('id')->on('socios')->onDelete('set null');
            $table->foreign('id_boleta')->references('id')->on('boletas')->onDelete('set null');

            $table->index('flow_order');
            $table->index('token');
            $table->index('estado');
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
