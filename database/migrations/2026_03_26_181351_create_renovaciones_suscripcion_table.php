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
        Schema::create('renovaciones_suscripcion', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_organizacion');
            $table->date('fecha_vencimiento');
            $table->date('fecha_procesada')->nullable();
            $table->decimal('monto', 10, 2);
            $table->enum('estado', ['pendiente', 'procesando', 'pagado', 'fallido', 'cancelado'])->default('pendiente');
            $table->string('metodo_pago')->nullable(); // flow, manual
            $table->string('token_flow')->nullable();
            $table->text('respuesta_flow')->nullable();
            $table->integer('intentos_notificacion')->default(0);
            $table->timestamp('notificado_7dias')->nullable();
            $table->timestamp('notificado_3dias')->nullable();
            $table->timestamp('notificado_1dia')->nullable();
            $table->timestamp('notificado_vencido')->nullable();
            $table->text('notas')->nullable();
            $table->timestamps();

            $table->foreign('id_organizacion')->references('id')->on('organizaciones')->onDelete('cascade');
            $table->index(['fecha_vencimiento', 'estado']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('renovaciones_suscripcion');
    }
};
