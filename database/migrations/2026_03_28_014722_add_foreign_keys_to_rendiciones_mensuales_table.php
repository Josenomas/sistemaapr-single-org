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
        Schema::table('rendiciones_mensuales', function (Blueprint $table) {
            $table->foreign(['id_usuario_cierre'])->references(['id'])->on('users')->onDelete('SET NULL');
            $table->foreign(['id_responsable'])->references(['id'])->on('users')->onDelete('SET NULL');
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
        Schema::table('rendiciones_mensuales', function (Blueprint $table) {
            $table->dropForeign('rendiciones_mensuales_id_usuario_cierre_foreign');
            $table->dropForeign('rendiciones_mensuales_id_responsable_foreign');
            $table->dropForeign('rendiciones_mensuales_id_organizacion_foreign');
        });
    }
};
