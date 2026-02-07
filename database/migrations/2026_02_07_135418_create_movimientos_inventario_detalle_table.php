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
        Schema::create('movimientos_inventario_detalle', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_movimiento')->comment('ID del movimiento de inventario');
            $table->unsignedBigInteger('id_producto')->comment('ID del producto');
            $table->decimal('cantidad', 10, 2)->comment('Cantidad movida');
            $table->decimal('cantidad_anterior', 10, 2)->comment('Stock anterior');
            $table->decimal('cantidad_nueva', 10, 2)->comment('Stock nuevo');
            $table->timestamps();

            // Índices para mejorar rendimiento
            $table->index('id_movimiento');
            $table->index('id_producto');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('movimientos_inventario_detalle');
    }
};
