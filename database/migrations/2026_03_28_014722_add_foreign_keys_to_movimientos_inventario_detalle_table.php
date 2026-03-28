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
        Schema::table('movimientos_inventario_detalle', function (Blueprint $table) {
            $table->foreign(['id_organizacion'])->references(['id'])->on('organizaciones')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('movimientos_inventario_detalle', function (Blueprint $table) {
            $table->dropForeign('movimientos_inventario_detalle_id_organizacion_foreign');
        });
    }
};
