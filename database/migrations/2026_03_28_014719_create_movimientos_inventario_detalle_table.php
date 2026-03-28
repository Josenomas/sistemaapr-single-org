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
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_organizacion')->nullable()->index();
            $table->unsignedBigInteger('id_movimiento')->index()->comment('ID del movimiento de inventario');
            $table->unsignedBigInteger('id_producto')->index()->comment('ID del producto');
            $table->decimal('cantidad', 10)->comment('Cantidad movida');
            $table->decimal('cantidad_anterior', 10)->comment('Stock anterior');
            $table->decimal('cantidad_nueva', 10)->comment('Stock nuevo');
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
        Schema::dropIfExists('movimientos_inventario_detalle');
    }
};
