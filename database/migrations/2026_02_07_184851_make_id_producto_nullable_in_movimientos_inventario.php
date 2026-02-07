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
        Schema::table('movimientos_inventario', function (Blueprint $table) {
            $table->unsignedBigInteger('id_producto')->nullable()->change();
            $table->decimal('cantidad', 10, 2)->nullable()->change();
            $table->decimal('cantidad_anterior', 10, 2)->nullable()->change();
            $table->decimal('cantidad_nueva', 10, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('movimientos_inventario', function (Blueprint $table) {
            $table->unsignedBigInteger('id_producto')->nullable(false)->change();
            $table->decimal('cantidad', 10, 2)->nullable(false)->change();
            $table->decimal('cantidad_anterior', 10, 2)->nullable(false)->change();
            $table->decimal('cantidad_nueva', 10, 2)->nullable(false)->change();
        });
    }
};
