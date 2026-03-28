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
        Schema::table('cortes_suministro', function (Blueprint $table) {
            $table->foreign(['id_organizacion'])->references(['id'])->on('organizaciones')->onDelete('CASCADE');
            $table->foreign(['id_ejecutor'], 'cortes_suministro_ibfk_2')->references(['id'])->on('funcionarios')->onDelete('SET NULL');
            $table->foreign(['id_socio'], 'cortes_suministro_ibfk_1')->references(['id'])->on('socios')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cortes_suministro', function (Blueprint $table) {
            $table->dropForeign('cortes_suministro_id_organizacion_foreign');
            $table->dropForeign('cortes_suministro_ibfk_2');
            $table->dropForeign('cortes_suministro_ibfk_1');
        });
    }
};
