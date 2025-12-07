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
        Schema::create('movimientos_inventario', function (Blueprint $table) {
            $table->id();
            $table->string('numero_movimiento', 50)->unique();
            $table->unsignedBigInteger('id_producto');
            $table->enum('tipo_movimiento', ['entrada', 'salida', 'ajuste']);
            $table->decimal('cantidad', 10, 2);
            $table->decimal('cantidad_anterior', 10, 2);
            $table->decimal('cantidad_nueva', 10, 2);
            $table->string('motivo', 200);
            $table->text('descripcion')->nullable();
            $table->unsignedBigInteger('id_responsable')->nullable();
            $table->string('destino', 200)->nullable()->comment('Lugar o persona que recibe (para salidas)');
            $table->string('documento_referencia', 100)->nullable()->comment('N° factura, orden de trabajo, etc.');
            $table->date('fecha_movimiento');
            $table->text('observaciones')->nullable();
            $table->boolean('activo')->default(1);
            $table->timestamp('fecha_creacion')->useCurrent();
            $table->timestamp('fecha_actualizacion')->useCurrent()->useCurrentOnUpdate();

            $table->index('numero_movimiento');
            $table->index('id_producto');
            $table->index('tipo_movimiento');
            $table->index('fecha_movimiento');
            $table->index('id_responsable');
            $table->index('activo');

            $table->foreign('id_producto')->references('id')->on('inventario')->onDelete('cascade');
            $table->foreign('id_responsable')->references('id')->on('funcionarios')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('movimientos_inventario');
    }
};
