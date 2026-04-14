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
        Schema::create('solicitudes_pago_manual', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_pago_suscripcion')->constrained('pagos_suscripcion')->onDelete('cascade');
            $table->foreignId('id_organizacion')->constrained('organizaciones')->onDelete('cascade');
            $table->string('comprobante_path')->nullable();
            $table->string('numero_operacion', 100)->nullable();
            $table->string('banco_origen', 100)->nullable();
            $table->date('fecha_transferencia')->nullable();
            $table->decimal('monto', 10, 2);
            $table->enum('estado', ['pendiente', 'aprobada', 'rechazada'])->default('pendiente');
            $table->foreignId('revisado_por')->nullable()->constrained('usuarios')->onDelete('set null');
            $table->text('motivo_rechazo')->nullable();
            $table->text('notas')->nullable();
            $table->timestamp('fecha_revision')->nullable();
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
        Schema::dropIfExists('solicitudes_pago_manual');
    }
};
