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
        Schema::table('historial_consumo', function (Blueprint $table) {
            $table->foreign(['id_organizacion'])->references(['id'])->on('organizaciones')->onDelete('CASCADE');
            $table->foreign(['id_lectura'], 'historial_consumo_ibfk_2')->references(['id'])->on('lecturas')->onDelete('CASCADE');
            $table->foreign(['id_socio'], 'historial_consumo_ibfk_1')->references(['id'])->on('socios')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('historial_consumo', function (Blueprint $table) {
            $table->dropForeign('historial_consumo_id_organizacion_foreign');
            $table->dropForeign('historial_consumo_ibfk_2');
            $table->dropForeign('historial_consumo_ibfk_1');
        });
    }
};
