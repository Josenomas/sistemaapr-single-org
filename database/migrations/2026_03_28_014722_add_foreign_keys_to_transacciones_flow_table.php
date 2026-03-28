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
        Schema::table('transacciones_flow', function (Blueprint $table) {
            $table->foreign(['id_organizacion'])->references(['id'])->on('organizaciones')->onDelete('CASCADE');
            $table->foreign(['id_boleta'], 'transacciones_flow_ibfk_2')->references(['id'])->on('boletas')->onDelete('SET NULL');
            $table->foreign(['id_socio'], 'transacciones_flow_ibfk_1')->references(['id'])->on('socios')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transacciones_flow', function (Blueprint $table) {
            $table->dropForeign('transacciones_flow_id_organizacion_foreign');
            $table->dropForeign('transacciones_flow_ibfk_2');
            $table->dropForeign('transacciones_flow_ibfk_1');
        });
    }
};
