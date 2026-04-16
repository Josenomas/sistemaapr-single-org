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
        Schema::create('solicitudes_compra_dominio', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_organizacion');
            $table->string('dominio_solicitado', 100);

            // Estados del proceso
            $table->enum('estado', [
                'solicitado',              // Usuario solicitó
                'verificado_disponible',   // Admin verificó disponible
                'verificado_ocupado',      // Admin verificó ocupado
                'pendiente_pago',          // Esperando pago
                'pagado',                  // Usuario pagó
                'comprado',                // Comprado en NIC Chile
                'activo',                  // Activo y funcionando
                'cancelado'                // Cancelado
            ])->default('solicitado');

            // Verificación manual del admin
            $table->unsignedBigInteger('verificado_por')->nullable();
            $table->timestamp('fecha_verificacion')->nullable();

            // Pago
            $table->decimal('monto', 10, 2)->default(20000.00);
            $table->string('metodo_pago', 50)->nullable(); // 'transferencia' o 'flow'
            $table->string('comprobante_pago', 255)->nullable(); // Path al archivo
            $table->timestamp('fecha_pago')->nullable();

            // Compra en NIC Chile
            $table->unsignedBigInteger('comprado_por')->nullable();
            $table->timestamp('fecha_compra_nic')->nullable();
            $table->string('comprobante_nic', 255)->nullable();
            $table->date('fecha_vencimiento')->nullable(); // +1 año

            // Activación
            $table->timestamp('fecha_activacion')->nullable();

            // Observaciones
            $table->text('observaciones')->nullable();

            $table->timestamps();

            // Índices y llaves foráneas
            $table->foreign('id_organizacion')->references('id')->on('organizaciones')->onDelete('cascade');
            $table->foreign('verificado_por')->references('id')->on('usuarios')->onDelete('set null');
            $table->foreign('comprado_por')->references('id')->on('usuarios')->onDelete('set null');

            $table->index('estado');
            $table->index('dominio_solicitado');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('solicitudes_compra_dominio');
    }
};
