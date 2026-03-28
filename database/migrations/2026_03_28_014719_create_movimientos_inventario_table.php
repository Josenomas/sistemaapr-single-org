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
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_organizacion')->nullable()->index();
            $table->string('numero_movimiento', 50)->unique();
            $table->unsignedBigInteger('id_producto')->nullable()->index();
            $table->enum('tipo_movimiento', ['entrada', 'salida', 'ajuste'])->index();
            $table->decimal('cantidad', 10)->nullable();
            $table->decimal('cantidad_anterior', 10)->nullable();
            $table->decimal('cantidad_nueva', 10)->nullable();
            $table->string('motivo', 200);
            $table->text('descripcion')->nullable();
            $table->unsignedBigInteger('id_responsable')->nullable()->index();
            $table->string('destino', 200)->nullable()->comment('Lugar o persona que recibe (para salidas)');
            $table->string('documento_referencia', 100)->nullable()->comment('N° factura, orden de trabajo, etc.');
            $table->date('fecha_movimiento')->index();
            $table->text('observaciones')->nullable();
            $table->boolean('activo')->default(true)->index();
            $table->timestamp('fecha_creacion')->useCurrent();
            $table->timestamp('fecha_actualizacion')->useCurrentOnUpdate()->useCurrent();

            $table->index(['numero_movimiento']);
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
