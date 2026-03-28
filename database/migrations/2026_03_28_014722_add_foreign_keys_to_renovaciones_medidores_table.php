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
        Schema::table('renovaciones_medidores', function (Blueprint $table) {
            $table->foreign(['id_organizacion'])->references(['id'])->on('organizaciones')->onDelete('CASCADE');
            $table->foreign(['id_tecnico'], 'renovaciones_medidores_ibfk_2')->references(['id'])->on('funcionarios')->onDelete('SET NULL');
            $table->foreign(['id_socio'], 'renovaciones_medidores_ibfk_1')->references(['id'])->on('socios')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('renovaciones_medidores', function (Blueprint $table) {
            $table->dropForeign('renovaciones_medidores_id_organizacion_foreign');
            $table->dropForeign('renovaciones_medidores_ibfk_2');
            $table->dropForeign('renovaciones_medidores_ibfk_1');
        });
    }
};
