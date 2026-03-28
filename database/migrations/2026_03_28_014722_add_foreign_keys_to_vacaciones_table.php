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
        Schema::table('vacaciones', function (Blueprint $table) {
            $table->foreign(['suplente'], 'vacaciones_ibfk_3')->references(['id'])->on('funcionarios')->onDelete('SET NULL');
            $table->foreign(['id_aprobador'], 'vacaciones_ibfk_2')->references(['id'])->on('funcionarios')->onDelete('SET NULL');
            $table->foreign(['id_organizacion'])->references(['id'])->on('organizaciones')->onDelete('CASCADE');
            $table->foreign(['id_funcionario'], 'vacaciones_ibfk_1')->references(['id'])->on('funcionarios')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vacaciones', function (Blueprint $table) {
            $table->dropForeign('vacaciones_ibfk_3');
            $table->dropForeign('vacaciones_ibfk_2');
            $table->dropForeign('vacaciones_id_organizacion_foreign');
            $table->dropForeign('vacaciones_ibfk_1');
        });
    }
};
